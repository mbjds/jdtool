<?php

namespace JDCustom;

class jdinit
{
	private static $instance;

    public function __construct()
    {
		$mods = new jd_mods();
	    add_action( 'wp_head', array($mods ,'add_custom_script_to_wp_head') );
	    new jd_admin_page();
	    new jd_checkout();

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
}