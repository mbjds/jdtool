<?php

namespace JDCustom;

class jd_adminViews
{
    public function __construct()
    {
        add_filter('manage_edit-shop_order_columns', [$this, 'bbloomer_add_new_order_admin_list_column']);

        add_action('manage_shop_order_posts_custom_column', [
            $this,
            'bbloomer_add_new_order_admin_list_column_content',
        ]);
    }

    public function bbloomer_add_new_order_admin_list_column($columns)
    {
        if ($columns['wc_actions']) {
            unset($columns['wc_actions']);
            $columns['invoice'] = __('Faktura', 'woocommerce');
            $columns['VIP'] = __('VIP', 'woocommerce');
            $columns['wc_actions'] = __('Actions', 'woocommerce');
        }

        unset($columns['shipping_address']);

        return $columns;
    }

    public function bbloomer_add_new_order_admin_list_column_content($column): void
    {
        global $post;

        if ('invoice' === $column) {
            $nip = get_post_meta($post->ID, 'vat_id', true);
            if ($nip) {
                echo '<span style="color: green; font-weight: bold">Tak</span>';
            } else {
                echo '-';
            }
        }
        if ('VIP' === $column) {
            $nip = get_post_meta($post->ID, 'has_vip', true);
            if ($nip) {
                echo '✔️';
            } else {
                echo '-';
            }
        }
    }
}
