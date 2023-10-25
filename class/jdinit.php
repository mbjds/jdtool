<?php

namespace JDCustom;

class jdinit
{

    public function __construct()
    {
		$mods = new jd_mods();
	    add_action( 'wp_head', array($mods ,'add_custom_script_to_wp_head') );
	    new jd_admin_page();
	    new jd_checkout();

    }

}