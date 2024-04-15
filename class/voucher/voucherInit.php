<?php

namespace JDCustom\voucher;

/**
 * Class voucherInit - Initialize the voucher post type.
 */
class voucherInit
{
    public function __construct()
    {
        add_action('init', [$this, 'aeroVouchers']);
        $post_type = 'aeroVouchers';

        // Register the columns.
        add_filter('manage_aerovouchers_posts_columns', function ($defaults) {
            unset($defaults);
            $defaults['cb'] = '<input type="checkbox" />';
            $defaults['orderid'] = 'Zamówienie';
            $defaults['voucherCode'] = 'Kod Vouchera';
            $defaults['vip'] = 'VIP';
            $defaults['status'] = 'Status';
            $defaults['dedication'] = 'Dedykacja';
            $defaults['created'] = 'Utworzono';
            $defaults['reservationDate'] = 'Data rezerwacji';
            $defaults['closed'] = 'Zakończono';
            $defaults['actions'] = ' ';

            return $defaults;
        });

        add_filter('manage_edit-aerovouchers_sortable_columns', function ($columns) {
            $columns['orderid'] = 'Zamówienie';
            $columns['voucherCode'] = 'Kod Vouchera';
            $columns['vip'] = 'VIP';
            $columns['status'] = 'Status';
            $columns['created'] = 'Utworzono';
            $columns['reservationDate'] = 'Data rezerwacji';
            $columns['closed'] = 'Zakończono';

            return $columns;
        });

        // Handle the value for each of the new columns.
        add_action('manage_aerovouchers_posts_custom_column', function ($column_name, $post_id) {
            $inst = new voucherInst($post_id);
            if ('orderid' == $column_name) {
                echo $inst->getOrderLink();
            }

            if ('voucherCode' == $column_name) {
                // Display an ACF field
                echo $inst->getMeta('voucherCode');
            }

            if ('vip' == $column_name) {
                // Display an ACF field
                $inst->renderBool((int) $inst->getMeta('vip'));
            }
            if ('status' == $column_name) {
                // Display an ACF field
                $inst->renderStatus();
            }
            if ('created' == $column_name) {
                // Display an ACF field
                $inst->renderDate($inst->getMeta('created'));
            }
            if ('dedication' == $column_name) {
                // Display an ACF field
                $inst->renderIsDedication();
            }
            if ('reservationDate' == $column_name) {
                // Display an ACF field
                $inst->renderDate($inst->getMeta('reservation'));
            }
            if ('closed' == $column_name) {
                // Display an ACF field
                $inst->renderDate($inst->getMeta('closed'));
            }

            if ('actions' == $column_name) {
                $ii = 2;
                echo '<i  data-id="'.$post_id.'" style="font-size: 22px; padding: 0 7px" class="rTrue editV  fa-regular  fa-pen-to-square"></i>';
                for ($i = 0; $i < $ii; ++$i) {
                    switch ($i) {
                        case 0:
                            if (0 == $inst->getMeta('vStatus')) {
                                $render = 'rTrue';
                            } else {
                                $render = 'rFalse';
                            }
                            echo '<i  data-id="'.$post_id.'" style="font-size: 22px; padding: 0 7px" class="'.$render.' activateF  fa-regular fa-check-circle"></i>';

                            break;

                        case 1:
                            if (0 == $inst->getMeta('vStatus')) {
                                $render = 'rFalse';
                            } else {
                                $render = 'rTrue';
                            }
                            echo '<i data-id="'.$post_id.'" data-code="'.$inst->getVoucherCode().'" style="font-size: 22px; padding: 0 7px" class="'.$render.' renderPDF fa-regular fa-file-pdf"></i>';

                            break;
                    }
                }
            }
        }, 10, 2);

        add_filter('template_include', [$this, 'custom_post_type_template']);
    }

    // Register Custom Post Type
    public function aeroVouchers()
    {
        $labels = [
            'name' => _x('Vouchery', 'Post Type General Name', 'jdtools'),
            'singular_name' => _x('Voucher', 'Post Type Singular Name', 'jdtools'),
            'menu_name' => __('Vouchery', 'jdtools'),
            'name_admin_bar' => __('Voucher', 'jdtools'),
            'archives' => __('Vouchers Archives', 'jdtools'),
            'attributes' => __('Item Attributes', 'jdtools'),
            'parent_item_colon' => __('Parent Item:', 'jdtools'),
            'all_items' => __('Wszystkie Vouchery', 'jdtools'),
            'add_new_item' => __('Dodaj', 'jdtools'),
            'add_new' => __('Dodaj', 'jdtools'),
            'new_item' => __('Nowy voucher', 'jdtools'),
            'edit_item' => __('Edytuj voucher', 'jdtools'),
            'update_item' => __('Aktualizuj voucher', 'jdtools'),
            'view_item' => __('View Item', 'jdtools'),
            'view_items' => __('View Items', 'jdtools'),
            'search_items' => __('Search Item', 'jdtools'),
            'not_found' => __('Not found', 'jdtools'),
            'not_found_in_trash' => __('Not found in Trash', 'jdtools'),
            'featured_image' => __('Featured Image', 'jdtools'),
            'set_featured_image' => __('Set featured image', 'jdtools'),
            'remove_featured_image' => __('Remove featured image', 'jdtools'),
            'use_featured_image' => __('Use as featured image', 'jdtools'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'jdtools'),
            'items_list' => __('Items list', 'jdtools'),
            'items_list_navigation' => __('Items list navigation', 'jdtools'),
            'filter_items_list' => __('Filter items list', 'jdtools'),
        ];
        $args = [
            'label' => __('Voucher', 'jdtools'),
            'description' => __('Vouchery', 'jdtools'),
            'labels' => $labels,
            'supports' => ['custom-fields'],
            'taxonomies' => [],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'menu_icon' => 'dashicons-tickets',
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'show_in_rest' => false,
        ];
        register_post_type('aeroVouchers', $args);
    }

    public function custom_post_type_template($template)
    {
        global $post;

        if ('aerovouchers' == $post->post_type) {
            $template_path = JD_PLUGIN_PATH.'/templates/single-aerovouchers.php';

            if (file_exists($template_path)) {
                return $template_path;
            }
        }

        return $template;
    }
}
