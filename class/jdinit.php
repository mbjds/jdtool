<?php
namespace JDCustom;
use WP_CLI;
class jdinit
{
	private static $instance;

    public function __construct()
    {
		$mods = new jd_mods();
	    new jd_admin_page();
	    new jd_checkout();
		new jd_assets();
		new jd_adminViews();
		new jd_cli();




    }
	public static function init()
	{
		// Check is $_instance has been set
		if(!isset(self::$instance))
		{
			// Creates sets object to instance
			self::$instance = new jdinit();
		}

		// Returns the instance
		return self::$instance;
	}

	/**
	 * @throws \Exception
	 */

}