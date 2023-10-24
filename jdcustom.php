<?php
/*
 * Plugin Name:       JDCustom
 * Plugin URI:        https://jds-group.eu
 * Description:       Implementacja customowych zamian
 * Version:           1.0.0
 * Requires PHP:      8.0
 * Author:            Marcin Bojaraski
 * Author URI:        https://jds-group.eu
 * License:           GPL v2 or later
 * Text Domain:       jdcustom
 * Domain Path:       /languages
 */

require 'vendor/autoload.php';

use JDCustom\jdHelpers;
function add_custom_script_to_wp_head() {?>
	<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            var  accept = "Akceptuj";
            var more = "Więcej...";
            document.getElementsByClassName('flatsome-cookies__accept-btn')[0].innerHTML = '<span> <?php echo get_option('cookie_accept') ?></span>';
            document.getElementsByClassName('flatsome-cookies__more-btn')[0].innerHTML = '<span> <?php echo get_option('cookie_more') ?></span>';
        });

	</script>
<?php
}
add_action( 'wp_head', 'add_custom_script_to_wp_head' );
function custom_phone_number_error_message($error) {
	if ($error === '<strong>Numer telefonu płatnika</strong> nie jest poprawnym numerem telefonu.') {
		$error = '';
	}
	return $error ;
}

add_filter('woocommerce_add_error', 'custom_phone_number_error_message', 10, 1);

add_action('woocommerce_checkout_process', 'jds_custom_checkout_field_process');

function jds_custom_checkout_field_process() {

    global $woocommerce;


    if ( ! (preg_match('/^(?:(?:\+|00)\d{2})?[ -]?(\d{2}[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}|\d{3}[ -]?\d{3}[ -]?\d{3})$/', $_POST['billing_phone'] ))){

        wc_add_notice( get_option('phone_notice')  ,'error' );
      
    }

    if($_POST['vat_no'] === '' && $_POST['vat_choose']){
	    wc_add_notice( get_option('vat_empty') ,'error' );

    }elseif($_POST['vat_no'] !== '' && !jdHelpers::checkNip($_POST['vat_no']) && $_POST['vat_choose']){
	    wc_add_notice( get_option('vat_invalid') ,'error' );

    }

    if(!$_POST['billing_company2'] && $_POST['vat_choose']){
        wc_add_notice(get_option('company_invalid'), 'error');
    }







}

function custom_enqueue_checkout_script() {
        if(is_checkout()){
	        wp_enqueue_script('jd-checkout-js', plugin_dir_url(__FILE__) . 'assets/js/jdcustom.js',null, '43.9');

        }
    wp_enqueue_script('js-general-js', plugin_dir_url(__FILE__) . 'assets/js/global.js', null, '2.1');


	wp_enqueue_style( 'jd-style', plugin_dir_url(__FILE__) . 'assets/css/jd-checkout.css', false, '1.1', 'all');

}
add_action('wp_enqueue_scripts', 'custom_enqueue_checkout_script');

new JDCustom\jd_admin_page();
new JDCustom\jd_checkout();