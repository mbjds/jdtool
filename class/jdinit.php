<?php
namespace JDCustom;
use JDCustom\voucher\adminView;
use JDCustom\voucher\aeroVoucher;
use JDCustom\voucher\voucherInit;
use rnwcinv\htmlgenerator\AreaGenerator;
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
		new jd_toolset();
		new voucherInit();
		$av = new aeroVoucher();

	    add_action( 'woocommerce_thankyou', array($av, 'generateVoucher' ));
	  //  add_action( 'woocommerce_after_order_itemmeta', array($this,'display_admin_order_item_custom_button', 10, 3 ));

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

	public function display_admin_order_item_custom_button( $item_id, $item, $product ){
		// Only "line" items and backend order page

//		$vv = wc_get_order_item_meta($item_id, '_vouchers'); // Get custom item meta data (array)


			// Display a custom download button using custom meta for the link
			echo 'test';

	}
}