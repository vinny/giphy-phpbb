(() => {
	'use strict';

	document.addEventListener('DOMContentLoaded', () => {
		const openBtn = document.getElementById('open-giphy-modal');
		if (!openBtn) return; // Exist if button isn't on the page

		const GIPHY_API_URL = openBtn.getAttribute('data-giphy-api');
		const GIPHY_SEARCH_URL = GIPHY_API_URL + (GIPHY_API_URL.indexOf('?') !== -1 ? '&' : '?') + 'action=search&q=';
		const GIPHY_TRENDING_URL = GIPHY_API_URL + (GIPHY_API_URL.indexOf('?') !== -1 ? '&' : '?') + 'action=trending';
		const GIPHY_TRENDING_ENABLED = openBtn.getAttribute('data-giphy-trending') === 'true';

		const LANG_APIKEY_UNAUTHORIZED = openBtn.getAttribute('data-lang-unauthorized');
		const LANG_APIKEY_INVALID = openBtn.getAttribute('data-lang-invalid');
		const LANG_USAGE_LIMIT = openBtn.getAttribute('data-lang-limit');
		const LANG_ERROR_CONNECT = openBtn.getAttribute('data-lang-connect');
		const LANG_NO_RESULTS = openBtn.getAttribute('data-lang-no-results');
		const LANG_SEARCH_ERROR = openBtn.getAttribute('data-lang-error');

		const modal = document.getElementById('giphy-modal');
		const closeBtn = modal.querySelector('.close-modal');
		const searchInput = document.getElementById('giphy-search');
		const resultsDiv = document.getElementById('giphy-results');

		let currentAbortController = null;

		function closeModal() {
			document.body.style.overflow = '';
			modal.classList.remove('active');
			if (currentAbortController) {
				currentAbortController.abort();
			}
		}

		function showError(message) {
			resultsDiv.innerHTML = '';
			const msg = document.createElement('p');
			msg.textContent = message;
			msg.style.color = 'red';
			msg.style.fontWeight = 'bold';
			resultsDiv.appendChild(msg);
		}

		function loadGifs(url) {
			if (currentAbortController) {
				currentAbortController.abort();
			}
			
			currentAbortController = new AbortController();
			const signal = currentAbortController.signal;

			fetch(url, { signal })
				.then(res => {
					if (res.status === 401) {
						throw new Error(LANG_APIKEY_UNAUTHORIZED);
					} else if (res.status === 403) {
						throw new Error(LANG_APIKEY_INVALID);
					} else if (res.status === 429) {
						throw new Error(LANG_USAGE_LIMIT);
					} else if (!res.ok) {
						throw new Error(LANG_ERROR_CONNECT + ' ' + res.status);
					}
					return res.json();
				})
				.then(data => {
					resultsDiv.innerHTML = '';

					if (!data.data || data.data.length === 0) {
						showError(LANG_NO_RESULTS);
						return;
					}

					data.data.forEach(gif => {
						const img = document.createElement('img');
						img.src = gif.images.fixed_height_small.url;

						img.title = gif.title ?? '';
						
						img.addEventListener('click', () => {
							const bbcode = '[img]' + gif.images.original.url + '[/img] ';
							if (typeof window.insert_text === 'function') {
								window.insert_text(bbcode);
							} else {
								let targetId = (typeof window.text_name !== 'undefined') ? window.text_name : 'message';
								const messageField = document.getElementById(targetId) 
													|| document.getElementById('signature') 
													|| document.querySelector('textarea');
								if (messageField) {
									messageField.value += (messageField.value.length > 0 && !messageField.value.endsWith(' ') ? ' ' : '') + bbcode;
									messageField.focus();
								}
							}
							
							closeModal();
						});
						resultsDiv.appendChild(img);
					});
				})
				.catch(err => {
					if (err.name === 'AbortError') return;
					showError(err.message);
					console.error(LANG_SEARCH_ERROR, err);
				});
		}

		openBtn.addEventListener('click', () => {
			document.body.style.overflow = 'hidden';
			modal.classList.add('active');
			searchInput.value = '';
			if (currentAbortController) currentAbortController.abort();
			searchInput.focus();
			if (GIPHY_TRENDING_ENABLED) {
				loadGifs(GIPHY_TRENDING_URL);
			} else {
				resultsDiv.innerHTML = '';
			}
		});

		closeBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
		
		modal.addEventListener('transitionend', (e) => {
			// Ensure cleanup only happens after the close animation completes on the overlay
			if (!modal.classList.contains('active') && e.target === modal && e.propertyName === 'opacity') {
				resultsDiv.innerHTML = '';
				searchInput.value = '';
			}
		});

		let searchTimeout;
		searchInput.addEventListener('keydown', (e) => {
			if (e.key === 'Enter') {
				e.preventDefault();
				clearTimeout(searchTimeout);
				const query = searchInput.value.trim();
				
				if (query) {
					loadGifs(GIPHY_SEARCH_URL + encodeURIComponent(query));
				} else if (GIPHY_TRENDING_ENABLED) {
					loadGifs(GIPHY_TRENDING_URL);
				} else {
					if (currentAbortController) currentAbortController.abort();
					resultsDiv.innerHTML = '';
				}
			}
		});

		searchInput.addEventListener('input', () => {
			clearTimeout(searchTimeout);
			const query = searchInput.value.trim();
			
			if (!query) {
				if (currentAbortController) currentAbortController.abort();
				
				if (GIPHY_TRENDING_ENABLED) {
					loadGifs(GIPHY_TRENDING_URL);
				} else {
					resultsDiv.innerHTML = '';
				}
				return;
			}

			// Debounce
			searchTimeout = setTimeout(() => {
				loadGifs(GIPHY_SEARCH_URL + encodeURIComponent(query));
			}, 500);
		});
	});

})();