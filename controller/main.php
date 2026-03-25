<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vinny\giphy\controller;

class main
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config             $config   Config object
	 * @param \phpbb\request\request_interface $request  Request object
	 * @param \phpbb\user                      $user     User object
	 * @param \phpbb\language\language         $language Language object
	 * @param \phpbb\auth\auth                 $auth     Auth object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request, \phpbb\user $user, \phpbb\language\language $language, \phpbb\auth\auth $auth)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->language = $language;
		$this->auth = $auth;
	}

	/**
	 * Handle API request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function handle()
	{
		$this->language->add_lang('common', 'vinny/giphy');

		// Check if the user has permission to post
		if (!$this->auth->acl_getf_global('f_post') && !$this->auth->acl_get('u_sendpm') && !$this->auth->acl_get('u_sig'))
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => $this->language->lang('GIPHY_API_UNAUTHORIZED')], 403);
		}

		$apikey = $this->config['giphy_apikey'];
		$limit = max(1, min(50, (int) $this->config['giphy_limit']));
		
		$action = $this->request->variable('action', 'trending');
		$query = $this->request->variable('q', '', true);

		if (empty($this->config['giphy_enable']))
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => $this->language->lang('GIPHY_DISABLED')], 403);
		}

		if (!$apikey)
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => $this->language->lang('GIPHY_APIKEY_MISSING')], 500);
		}

		if ($action === 'search' && $query)
		{
			$url = 'https://api.giphy.com/v1/gifs/search?api_key=' . urlencode($apikey) . '&limit=' . $limit . '&q=' . urlencode($query);
		}
		else
		{
			if (empty($this->config['giphy_trending']))
			{
				return new \Symfony\Component\HttpFoundation\JsonResponse(['data' => []], 200);
			}

			$url = 'https://api.giphy.com/v1/gifs/trending?api_key=' . urlencode($apikey) . '&limit=' . $limit;
		}

		// Use stream context to ignore HTTP errors so we can fetch the body and forward the status code
		$options = [
			'http' => [
				'method'		=> 'GET',
				'header'		=> "Content-type: application/x-www-form-urlencoded\r\n",
				'ignore_errors'	=> true,
				'timeout'		=> 10
			]
		];

		$context  = stream_context_create($options);

		set_error_handler(function($errno, $errstr) {
			throw new \RuntimeException($errstr, $errno);
		});

		try {
			$result = file_get_contents($url, false, $context);
		} catch (\RuntimeException $e) {
			$result = false;
		}

		restore_error_handler();

		if ($result === false)
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => $this->language->lang('GIPHY_API_FAILED')], 500);
		}

		$statusCode = 200;
		if (isset($http_response_header) && is_array($http_response_header))
		{
			foreach ($http_response_header as $header)
			{
				if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches))
				{
					$statusCode = (int) $matches[1];
					break;
				}
			}
		}

		// Default to empty array structured response on decode failure or parse errors
		$data = json_decode($result, true) ?: [];
		if (json_last_error() !== JSON_ERROR_NONE || !isset($data['data']))
		{
			$data = ['error' => $this->language->lang('GIPHY_INVALID_JSON')];
			$statusCode = 500;
		}

		return new \Symfony\Component\HttpFoundation\JsonResponse($data, $statusCode);
	}
}
