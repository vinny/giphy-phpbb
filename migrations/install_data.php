<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vinny\giphy\migrations;

class install_data extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->config->offsetExists('giphy_enable');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_data()
	{
		return [
			// Configs
			['config.add', ['giphy_enable', 0]],
			['config.add', ['giphy_apikey', '']],
			['config.add', ['giphy_trending', 1]],
			['config.add', ['giphy_limit', 25]],

			// Add a parent module (ACP_GIPHY_TITLE) to the Extensions tab (ACP_CAT_DOT_MODS)
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GIPHY_TITLE',
			]],

			// Add our main_module to the parent module (ACP_GIPHY_TITLE)
			['module.add', [
				'acp',
				'ACP_GIPHY_TITLE',
				[
					'module_basename'	=> '\vinny\giphy\acp\main_module',
					'modes'				=> ['settings'],
				],
			]],
		];
	}
}
