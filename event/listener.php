<?php
/**
 *
 * Giphy extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025, Vinny, https://github.com/vinny
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace vinny\giphy\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config       $config   Config object
	 * @param \phpbb\language\language   $language Language object
	 * @param \phpbb\template\template   $template Template object
	 * @param \phpbb\controller\helper   $helper   Controller helper
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\controller\helper $helper)
	{
		$this->config = $config;
		$this->language = $language;
		$this->template = $template;
		$this->helper = $helper;
	}

		/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.page_header'	=> 'add_giphy_button',
		];
	}

	/**
	 * Set Giphy template data
	 *
	 * @return void
	 * @access public
	 */
	public function add_giphy_button()
	{
		if (!$this->config['giphy_enable'])
		{
			return;
		}

		$this->language->add_lang('common', 'vinny/giphy');
		$this->template->assign_vars([
			'U_GIPHY_API' => htmlspecialchars_decode($this->helper->route('vinny_giphy_api')),
			'S_GIPHY_TRENDING' => (bool) $this->config['giphy_trending'],
		]);
	}
}