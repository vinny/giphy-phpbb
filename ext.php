<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vinny\giphy;

/**
 * Extension class for custom enable/disable/purge actions
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Enable extension if phpBB minimum version requirement is met
	 *
	 * Requires >= phpBB 3.3.0 and < 4.0.0-dev
	 *
	 * @return bool
	 * @access public
	 */
	public function is_enableable()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.3.0', '>=')
			&& phpbb_version_compare(PHPBB_VERSION, '4.0.0-dev', '<');
	}
}
