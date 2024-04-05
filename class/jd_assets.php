<?php

namespace JDCustom;

class jd_assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'custom_enqueue_checkout_script']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_custom_css_js']);
    }

    public function enqueue_admin_custom_css_js(): void
    {
        wp_enqueue_style('jd-style', plugin_dir_url(__DIR__).'assets/scss/admin.css', [], date('YmdHi'));
        wp_enqueue_style('fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
        wp_enqueue_script('jd-admin-js', plugin_dir_url(__DIR__).'assets/js/admin.js', [], date('YmdHi'));
    }

    public function custom_enqueue_checkout_script(): void
    {
        if (is_checkout()) {
            wp_enqueue_script('jd-checkout-js', plugin_dir_url(__DIR__).'assets/js/jdcustom.js', null, '45');
        }
        wp_enqueue_script('js-general-js', plugin_dir_url(__DIR__).'assets/js/global.js', null, '2.1');

        wp_enqueue_style('jd-style', plugin_dir_url(__DIR__).'assets/css/jd-checkout.css', false, '1.3', 'all');
    }
}
