<?php

namespace JDCustom;

use JDCustom\ajax\jdAjax;
use JDCustom\order\wcOverride;
use JDCustom\voucher\aeroVoucher;
use JDCustom\voucher\voucherInit;

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
        new jdAjax();
        $over = new wcOverride();
        $help = new jdHelpers();
        add_action('woocommerce_thankyou', [$av, 'generateVoucher']);
        add_action('woocommerce_order_status_changed', [$over, 'closeAfterPayment'], 10, 3);
        add_action('woocommerce_email_before_send', [$help, 'logMailAction'], 10, 4);
    }

    public static function init()
    {
        // Check is $_instance has been set
        if (!isset(self::$instance)) {
            // Creates sets object to instance
            self::$instance = new jdinit();
        }

        // Returns the instance
        return self::$instance;
    }
}
