<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vinny\giphy\acp;

class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\vinny\giphy\acp\main_module',
			'title'		=> 'ACP_GIPHY_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_GIPHY_SETTINGS',
					'auth'	=> 'ext_vinny/giphy && acl_a_board',
					'cat'	=> ['ACP_CAT_DOT_MODS'],
				],
			],
		];
	}
}
