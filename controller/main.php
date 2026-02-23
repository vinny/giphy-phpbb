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

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config             $config  Config object
	 * @param \phpbb\request\request_interface $request Request object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request)
	{
		$this->config = $config;
		$this->request = $request;
	}

	/**
	 * Handle API request
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function handle()
	{
		$apikey = $this->config['giphy_apikey'];
		$limit = max(1, min(50, (int) $this->config['giphy_limit']));
		
		$action = $this->request->variable('action', 'trending');
		$query = $this->request->variable('q', '', true);

		if (empty($this->config['giphy_enable']))
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => 'Giphy integration is disabled.'], 403);
		}

		if (!$apikey)
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => 'API key is not configured.'], 500);
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
				'method' => 'GET',
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'ignore_errors' => true
			]
		];

		$context  = stream_context_create($options);
		$result = @file_get_contents($url, false, $context);

		if ($result === false)
		{
			return new \Symfony\Component\HttpFoundation\JsonResponse(['error' => 'API request failed.'], 500);
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
			$data = ['error' => 'Invalid JSON from API'];
			$statusCode = 500;
		}

		return new \Symfony\Component\HttpFoundation\JsonResponse($data, $statusCode);
	}
}
