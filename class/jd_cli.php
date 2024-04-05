<?php

namespace JDCustom;

class jd_cli
{
    public function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            $this->add_wp_cli_command();
        }
    }

    public function add_wp_cli_command()
    {
        if (class_exists('WP_CLI')) {
            \WP_CLI::add_command('jds markVIP', [$this, 'markVIP']);
            \WP_CLI::add_command('jds markNonVIP', [$this, 'markNonVIP']);
            \WP_CLI::add_command('jds close', [$this, 'closeOutdatedd']);
        }
    }

    public function markVIP($args, $assoc_args)
    {
        // Kod obsługujący polecenie WP-CLI
        \WP_CLI::success(jd_toolset::getVipIDs());
        \WP_CLI::success('Zamówienia oznaczone poprawnie!');
    }

    public function markNonVIP($args, $assoc_args)
    {
        // Kod obsługujący polecenie WP-CLI
        \WP_CLI::success(jd_toolset::getNotVipIDs());
        \WP_CLI::success('Zamówienia oznaczono porawnie!');
    }

    public function closeOutdatedd()
    {
        $two_years_ago = new \DateTime('-2 years');
        $two = $two_years_ago->format('d-m-Y');
        $query = new \WC_Order_Query([
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => ['wc-processing'],
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

            //	self::nicedump($order);
        }

        return $orders;
    }
}
