<?php
/*
 * Plugin Name:       JDCustom
 * Plugin URI:        https://jds-group.eu
 * Description:       Implementacja customowych zamian
 * Version:           1.9.9
 * Requires PHP:      8.1
 * Author:            Marcin Bojarski
 * Author URI:        https://jds-group.eu
 * License:           GPL v2 or later
 * Text Domain:       jdcustom
 * Domain Path:       /languages
 */

require __DIR__ . '/vendor/autoload.php';

use JDCustom\jdHelpers;
use JDCustom\jdinit;

// Add custom meta box to WooCommerce orders page

/**
 * Add custom meta box.
 *
 * @return void
 */


const JD_PLUGIN_PATH = __DIR__;
define("JD_UPLOAD_PATH", dirname(JD_PLUGIN_PATH) . '/uploads/');
function custom_phone_number_error_message($error)
{
    if ('<strong>Numer telefonu płatnika</strong> nie jest poprawnym numerem telefonu.' === $error || '<strong>Numer telefonu płatnika</strong> jest wymaganym polem.' === $error) {
        $error = '';
    }

    return $error;
}

add_filter('woocommerce_add_error', 'custom_phone_number_error_message', 10, 1);

add_action('woocommerce_checkout_process', 'jds_custom_checkout_field_process');

function jds_custom_checkout_field_process()
{
    global $woocommerce;

    if (! preg_match(
        '/^(?:(?:\+|00)\d{2})?[ -]?(\d{2}[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}|\d{3}[ -]?\d{3}[ -]?\d{3})$/',
        $_POST['billing_phone']
    )) {
        wc_add_notice(get_option('phone_notice'), 'error');
    }

    if ('' === $_POST['vat_no'] && $_POST['vat_choose']) {
        wc_add_notice(get_option('vat_empty'), 'error');
    } elseif ('' !== $_POST['vat_no'] && ! jdHelpers::checkNip($_POST['vat_no']) && $_POST['vat_choose']) {
        wc_add_notice(get_option('vat_invalid'), 'error');
    }

    if (! $_POST['billing_company2'] && $_POST['vat_choose']) {
        wc_add_notice(get_option('company_invalid'), 'error');
    }
}

function custom_wc_order_method()
{
    // Kod funkcji - możesz dodać tu dowolną logikę
    return 'Niestandardowa metoda dla zamówienia!';
}

// Hook do dodania funkcji do klasy WC_Order
function add_custom_wc_order_method()
{
    add_action('woocommerce_order_details_after_order_table', 'custom_wc_order_method');
}

add_action('woocommerce_init', 'add_custom_wc_order_method');

jdinit::init();
