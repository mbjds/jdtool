<?php

namespace JDCustom;

class jd_checkout
{
    public function __construct()
    {
        add_action('woocommerce_before_order_notes', [$this, 'jd_vat_checkbox']);
        add_action('woocommerce_before_order_notes', [$this, 'jd_vat_inp']);
        add_action('woocommerce_before_order_notes', [$this, 'jd_billing_company']);
        add_action('woocommerce_before_order_notes', [$this, 'jd_invoice_address_checkbox']);
        add_action('woocommerce_before_order_notes', [$this, 'jd_invoice_address']);
        add_filter('woocommerce_checkout_fields', [$this, 'jd_edit_checkout_fields'], 50);
        add_filter('woocommerce_checkout_fields', [$this, 'jd_remove_checkout_fields'], 5);
        add_filter('woocommerce_billing_fields', [$this, 'jd_reorder_checkout_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_custom_checkout_field']);
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_custom_field_on_order']);
    }

    public function jd_reorder_checkout_fields($fields)
    {
        $fields['billing_email']['priority'] = 5;
        $fields['billing_phone']['priority'] = 25;
        $fields['billing_address_1']['priority'] = 30;
        $fields['billing_city']['priority'] = 25;
        $fields['billing_postcode']['priority'] = 55;
        $fields['billing_country']['priority'] = 90;

        return $fields;
    }

    public function jd_edit_checkout_fields($fields)
    {
        $fields['billing']['billing_email']['label'] = 'Twój adres e-mail (Aby wysyłka automatyczna przebiegła poprawnie staraj się nie używać adresów z:  wp.pl, onet, interia, poczta.fm. Zalecamy pocztę na gmail. :) Jeśli bilet nie dotrze w max 5 min sprawdź SPAM i OFERTY! )';
        $fields['billing']['billing_address_1']['class'] = ['form-row-wide', 'jd-row-wide'];
        $fields['order']['order_comments']['label'] = 'Dane osoby obdarowanej (pozostaw pole puste jeśli chcesz voucher bez danych) ';
        $fields['order']['order_comments']['placeholder'] = 'Tu wpisz, imię, nazwisko, lub życzenia dla osoby obdarowanej.';

        return $fields;
    }

    public function jd_remove_checkout_fields($fields)
    {
        unset($fields['billing']['billing_address_2']);

        // unset( $fields['billing']['billing_country'] );
        return $fields;
    }

    public function jd_vat_checkbox($checkout)
    {
        woocommerce_form_field('vat_choose', [
            'type' => 'checkbox',
            'class' => ['form-row-wide'],
            'label' => 'Chcę fakturę VAT',
            'required' => false,
            'id' => 'vat_choose',
            'value' => 'false',
            'default' => 'false',
        ], $checkout->get_value('vat_choose'));
    }

    public function jd_invoice_address_checkbox($checkout)
    {
        woocommerce_form_field('invoice_choose', [
            'type' => 'checkbox',
            'class' => ['form-row-wide vat-hidden'],
            'label' => 'Adres do faktury jest taki sam jak zamówienia',
            'required' => false,
            'id' => 'invoice_choose',
            'value' => 'true',
            'default' => 'true',
        ], $checkout->get_value('invoice_choose'));
    }

    public function jd_invoice_address($checkout)
    {
        woocommerce_form_field('invoice_addr', [
            'type' => 'text',
            'class' => ['form-row-wide vat-hidden addr-hidden'],
            'label' => 'Ulica',
            'required' => true,
            'id' => 'invoice_addr',
        ], $checkout->get_value('invoice_addr'));

        woocommerce_form_field('invoice_zipcode', [
            'type' => 'text',
            'class' => ['form-row-wide vat-hidden addr-hidden'],
            'label' => 'Kod pocztowy',
            'required' => true,
            'id' => 'invoice_zipcode',
        ], $checkout->get_value('invoice_zipcode'));

        woocommerce_form_field('invoice_city', [
            'type' => 'text',
            'class' => ['form-row-wide vat-hidden addr-hidden'],
            'label' => 'Miasto',
            'required' => true,
            'id' => 'invoice_city',
        ], $checkout->get_value('invoice_city'));
    }

    public function jd_vat_inp($checkout)
    {
        woocommerce_form_field('vat_no', [
            'type' => 'text',
            'class' => ['form-row-wide vat-hidden'],
            'label' => 'Numer NIP',
            'required' => true,
            'id' => 'vat_no',
        ], $checkout->get_value('vat_no'));
    }

    public function jd_billing_company($checkout)
    {
        woocommerce_form_field('billing_company2', [
            'type' => 'text',
            'class' => ['form-row-wide vat-hidden'],
            'label' => 'Nazwa firmy',
            'required' => true,
            'id' => 'billing_company',
        ], $checkout->get_value('billing_company2'));
    }

    public function save_custom_checkout_field($order_id)
    {
        if (!empty($_POST['vat_no'])) {
            update_post_meta($order_id, 'vat_id', sanitize_text_field($_POST['vat_no']));
        }
        if (!empty($_POST['billing_company2'])) {
            update_post_meta($order_id, 'company_name', sanitize_text_field($_POST['billing_company2']));
        }
        if (!empty($_POST['vat_choose'])) {
            update_post_meta($order_id, 'invoice', sanitize_text_field($_POST['vat_choose']));
        }
        if (!empty($_POST['invoice_choose'])) {
            update_post_meta($order_id, 'custom_address_invoice', sanitize_text_field($_POST['invoice_choose']));
        }
        if (!empty($_POST['invoice_addr'])) {
            update_post_meta($order_id, 'invoice_address', sanitize_text_field($_POST['invoice_addr']));
        }
        if (!empty($_POST['invoice_zipcode'])) {
            update_post_meta($order_id, 'invoice_zipcode', sanitize_text_field($_POST['invoice_zipcode']));
        }
        if (!empty($_POST['invoice_city'])) {
            update_post_meta($order_id, 'invoice_city', sanitize_text_field($_POST['invoice_city']));
        }
    }

    public function display_custom_field_on_order($order)
    {
        if (get_post_meta($order->get_id(), 'invoice', true)) {
            echo '<h3>Dane do faktury</h3>';
            echo '<br>';
            echo '<div style="color: #777">';
            echo '<strong>Nip:</strong> '.get_post_meta($order->get_id(), 'vat_id', true).'</br>';
            echo '<br>';

            echo '<strong>Nazwa firmy:</strong> </br>';
            echo get_post_meta($order->get_id(), 'company_name', true).'</br>';
            echo '<br>';
            if (!get_post_meta($order->get_id(), 'custom_address_invoice', true)) {
                echo '<strong>Adres:</strong></br> ';
                echo get_post_meta($order->get_id(), 'invoice_address', true).'</br>';
                echo get_post_meta($order->get_id(), 'invoice_zipcode', true).'</br>';
                echo get_post_meta($order->get_id(), 'invoice_city', true);
            }
            echo '</div>';
        }
    }
}
