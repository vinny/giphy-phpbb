<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_GIPHY_TITLE'				=> 'Giphy',
	'ACP_GIPHY_SETTINGS'			=> 'Giphy Settings',
	'ACP_GIPHY_SETTINGS_EXPLAIN'	=> 'Here you can change the global settings for the Giphy integration module.',
	
	'ACP_GIPHY_ENABLE'				=> 'Enable Giphy',
	
	'ACP_GIPHY_TRENDING'			=> 'Enable Trending GIFs',
	'ACP_GIPHY_TRENDING_EXPLAIN'	=> 'Fetch GIFs currently trending online. Hand curated by the Giphy editorial team. The data returned mirrors the GIFs showcased on the Giphy homepage.<br>If disabled, users will only be able to perform manual searches.',
	
	'ACP_GIPHY_APIKEY'				=> 'Giphy API Key',
	'ACP_GIPHY_APIKEY_EXPLAIN'		=> 'Enter your Giphy API Key from <a href="https://developers.giphy.com/dashboard/?create=true" target="_blank">developer.giphy.com</a> to enable GIF search.',
	'ACP_GIPHY_LIMIT'				=> 'Number of items',
	'ACP_GIPHY_LIMIT_EXPLAIN'		=> 'The maximum number of items to display. (maximum limit: “50”)',
	'ACP_GIPHY_ERROR_APIKEY'		=> 'You cannot enable Giphy integration without providing an API Key.',
));
