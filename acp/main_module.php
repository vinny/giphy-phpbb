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

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $language, $template, $request, $config;

		$this->tpl_name = 'acp_giphy_body';
		$this->page_title = $language->lang('ACP_GIPHY_SETTINGS');

		add_form_key('vinny_giphy_settings');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('vinny_giphy_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			$enable = $request->variable('giphy_enable', 0);
			$apikey = $request->variable('giphy_apikey', '', true);

			if ($enable && empty($apikey))
			{
				trigger_error($language->lang('ACP_GIPHY_ERROR_APIKEY') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$config->set('giphy_enable', $enable);
			$config->set('giphy_apikey', $apikey);
			$config->set('giphy_trending', $request->variable('giphy_trending', 0));
			
			$giphy_limit = $request->variable('giphy_limit', 25);
			$config->set('giphy_limit', max(1, min(50, $giphy_limit)));

			trigger_error($language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$template->assign_vars([
			'GIPHY_ENABLE'		=> (bool) $config['giphy_enable'],
			'GIPHY_APIKEY'		=> $config['giphy_apikey'],
			'GIPHY_TRENDING'	=> (bool) $config['giphy_trending'],
			'GIPHY_LIMIT'		=> (int) $config['giphy_limit'],

			'U_ACTION'			=> $this->u_action,
		]);
	}
}
