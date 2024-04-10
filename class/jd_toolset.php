<?php

namespace JDCustom;

class jd_toolset
{
    public function __construct()
    {
        add_shortcode('jds', [$this, 'shor']);
    }

    /**
     * @param $var varaible to dump
     *
     * Wrapper for var_dump with <pre> tag - for better readability
     */
    public static function nicedump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    /**
     * @return array ids of orders without VIP flag
     */
    public static function getOrdersWithoutVIPflag()
    {
        $args = [
            'limit' => -1,
            'return' => 'ids',
            'meta_key' => 'has_vip',
            'meta_compare' => 'not exists',
            'type' => 'shop_order', // filtered refunded orders
        ];

        return wc_get_orders($args);
    }

    /**
     * @return array|object|\stdClass[] number of altered orders
     */
    public static function getVipIDs()
    {
        global $wpdb;
        $ids = self::getOrdersWithoutVIPflag();
        $i = implode(',', $ids);
        $sql = "select distinct order_id from dlaextremalnych_woocommerce_order_items where order_id in ({$i}) and order_item_type = 'line_item' and order_item_name LIKE '%vip%' ";
        $resp = $wpdb->get_results($sql, ARRAY_A);

        return $resp;
    }

    public static function getNotVipIDs(): null|array|object
    {
        global $wpdb;
        $ids = self::getOrdersWithoutVIPflag();
        $i = implode(',', $ids);
        $sql = "select distinct order_id from dlaextremalnych_woocommerce_order_items where order_id in ({$i}) and order_item_type = 'line_item' and order_item_name NOT LIKE '%vip%' ";
        $resp = $wpdb->get_results($sql, ARRAY_A);

        return $resp;
    }

    public static function markOrders(array $ids, bool $vip): void
    {
        $vip = $vip ? true : false;
        foreach ($ids as $r) {
            if (!get_post_meta($r['order_id'], 'has_vip', true)) {
                update_post_meta($r['order_id'], 'has_vip', $vip);
            }
        }
    }

    /**
     * @return null|int number of altered orders
     */
    public static function closeOutdated()
    {
        $two_years_ago = new \DateTime('-2 years');
        $two = $two_years_ago->format('d-m-Y');
        $query = new \WC_Order_Query([
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => ['wc-processing', 'wc-pending'],
            'date_created' => '<'.$two,
            'meta_key' => 'has_vip',
            'meta_value' => false,
            'meta_compare' => '=',

            'return' => 'ids',
        ]);
        $orders = $query->get_orders();

        foreach ($orders as $id) {
            $order = wc_get_order($id);

            $order->add_order_note('Mineła ważność vouchera', false);
            $order->set_status('wc-completed');
            $order->save();
        }

        return $orders;
    }

    /**
     * Mark orders without the 'has_vip' flag.
     *
     * @return array altered orders for VIP and NOT VIP flag
     */
    public static function markVipFlag(): array
    {
        $vip = self::getVipIDs();
        $nvip = self::getNotVipIDs();

        return ['VIP' => $vip, 'NOT_VIP' => $nvip];
    }

    // Shortcode for testing purposes
    public static function shor() {}
}
