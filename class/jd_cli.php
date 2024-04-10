<?php

namespace JDCustom;

use JDCustom\order\process;
use JDCustom\voucher\aeroVoucher;
use WP_CLI;

class jd_cli
{
    private jdLog $log;

    public function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            $this->add_wp_cli_command();
        }
        $this->log = new jdLog('migrete-log');
    }

    public static function msg($message)
    {
        self::log()->logInfo($message);
        \WP_CLI::line($message);
    }

    public static function success($message)
    {
        self::log()->logInfo($message);
        \WP_CLI::success($message);
    }

    public static function spacer()
    {
        self::log()->logInfo('');
        \WP_CLI::line('');
    }

    public function add_wp_cli_command()
    {
        if (class_exists('WP_CLI')) {
            \WP_CLI::add_command('jds markOrders', [$this, 'markOrders']);
            \WP_CLI::add_command('jds closeOutdated', [$this, 'closeOutdated']);
            \WP_CLI::add_command('jds prepareItems', [$this, 'setAttrsForItems']);
            \WP_CLI::add_command('jds importVipVouchers', [$this, 'importVIPVouchers']);
            \WP_CLI::add_command('jds importNonVipVouchers', [$this, 'importNonVIPVouchers']);
            \WP_CLI::add_command('jds dev dropVouchers', [$this, 'removeVouchers']);
            \WP_CLI::add_command('jds dev cleanUpOrders', [$this, 'cleanUPorders']);
        }
    }

    public function markOrders($args, $assoc_args)
    {
        $vIds = jd_toolset::getVipIDs();
        $nIds = jd_toolset::getNotVipIDs();
        $all = count(jd_toolset::getOrdersWithoutVIPflag()) ?: 0;
        $v = count($vIds) ?: 0;
        $n = count($nIds) ?: 0;
        self::spacer();
        self::spacer();
        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');
        self::spacer();
        self::msg('Zamówienia bez wymaganej flagi:         '.$all);
        self::msg('Zamówienia do oznaczenia jako VIP:      '.$v);
        self::msg('Zamówienia do oznaczenia jako non-VIP:  '.$n);
        self::spacer();
        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');

        if (0 == $all) {
            \WP_CLI::line(' ');

            \WP_CLI::line(' ');

            self::msg('--------------------------------------------');
            self::msg('--------------------------------------------');
            self::msg('');

            self::success('        Brak zamówień do oznaczenia ');
            self::msg('');

            self::msg('--------------------------------------------');
            self::msg('--------------------------------------------');

            return;
        }
        self::spacer();
        self::spacer();
        \WP_CLI::confirm('Czy na pewno chcesz kontynuować?');

        $progress = WP_CLI\Utils\make_progress_bar('Oznaczanie zamówien VIP: ', count($vIds));

        self::spacer();
        self::spacer();
        foreach ($vIds as $i) {
            if (!get_post_meta($i['order_id'], 'has_vip', true)) {
                update_post_meta($i['order_id'], 'has_vip', true);
            }
            $progress->tick();
        }
        $progress->finish();
        self::spacer();
        self::spacer();
        \WP_CLI::success('Oznaczono: '.count($vIds).' zamówień  VIP');

        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');
        self::spacer();
        $progress = WP_CLI\Utils\make_progress_bar('Oznaczanie zamówien non VIP: ', count($nIds));

        self::spacer();
        self::spacer();
        $lp = 0;
        foreach ($nIds as $i) {
            if (!get_post_meta($i['order_id'], 'has_vip', true)) {
                update_post_meta($i['order_id'], 'has_vip', false);
                ++$lp;
            }
            $progress->tick();
        }
        $progress->finish();
        self::spacer();
        self::spacer();
        self::success('Oznaczono: '.count($nIds).' zamówień  non VIP');
        self::spacer();
        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');

        \WP_CLI::success('Zamówienia oznaczono porawnie!');
    }

    public function closeOutdated()
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

        $ids = $query->get_orders();

        self::spacer();
        self::spacer();
        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');
        self::spacer();
        self::msg('Zamówienia zamknięcia:                 '.count($ids));
        self::spacer();
        self::msg('--------------------------------------------');
        self::msg('--------------------------------------------');

        $progress = WP_CLI\Utils\make_progress_bar('Zamykanie zamówień:', count($ids));
        foreach ($ids as $id) {
            $o = new \WC_Order($id);
            $o->add_order_note('Mineła ważność vouchera', '0');
            wp_update_post(['ID' => $id, 'post_status' => 'wc-completed']);

            $progress->tick();
        }
        $progress->finish();

        self::spacer();
        self::spacer();
        self::success('Zamknięto wszystkie zamówienia po terminie '.count($ids).')');
        self::spacer();
        self::spacer();
    }

    public function setAttrsForItems()
    {
        self::msg('Przygotowywanie atrybutów dla produktów');
        $products = wc_get_products([
            'limit' => -1,
        ]);
        self::msg('Ilość produktów do edycji: '.count($products));

        \WP_CLI::confirm('Czy na pewno chcesz kontynuować?');
        foreach ($products as $product) {
            self::msg('Przygotowywanie atrybutów dla produktu: '.$product->get_name());

            $product->set_sold_individually(false);
            $product->set_manage_stock(false);
            $product->save();
        }

        $this->log->logInfo('Atrybuty produktów zostały zaktualizowane');
        \WP_CLI::success('Atrybuty produktów zostały zaktualizowane');
    }

    public function removeVouchers()
    {
        $all = get_posts(['post_type' => 'aerovouchers', 'numberposts' => -1, 'fields' => 'ids']);
        if (0 == count($all)) {
            self::spacer();
            self::spacer();

            self::success('Brak voucherów do usunięcia');
            self::spacer();

            self::spacer();

            return;
        }
        self::msg('Usuwanie wszystkich voucherów');
        self::spacer();
        \WP_CLI::confirm('Czy na pewno chcesz usunąć wszystkie vouchery? ');
        self::spacer();
        $progress = WP_CLI\Utils\make_progress_bar('Usuwanie voucherów', count($all));
        for ($i = 0; $i < count($all); ++$i) {
            wp_delete_post($all[$i], true);
            $progress->tick();
        }
        $progress->finish();
        self::spacer();
        self::spacer();
        self::success('Usunięto wszystkie vouchery ('.count($all).')');
        self::spacer();
        self::spacer();
    }

    public function cleanUPorders()
    {
        $sql = 'delete from dlaextremalnych_postmeta where meta_key = "has_vip" or meta_key = "hasVouchers"';
        global $wpdb;
        self::spacer();
        self::spacer();
        self::msg('Usuwanie flag z zamówień - czysty import i konwersja danych');
        self::spacer();
        self::spacer();
        \WP_CLI::confirm('Czy na pewno chcesz usunąć wszystkie flagi z zamówień?');
        self::spacer();
        self::spacer();
        $t = $wpdb->query($sql);

        self::success('Usunięto flagi z zamówień ('.$t.')');
    }

    public function importVIPVouchers()
    {
        $search = new process();
        $search->setDefaultQuertAttrs();
        $search->setVIPfilter();
        $ids = $search->getOrdersIDsToProcess();
        $a = new aeroVoucher();
        self::spacer();
        self::spacer();
        $progress = WP_CLI\Utils\make_progress_bar('Importowanie voucherów VIP: ', count($ids));

        self::spacer();
        self::spacer();
        for ($i = 0; $i < count($ids); ++$i) {
            $a->generateVoucher($ids[$i], true);
            wp_update_post(['ID' => $ids[$i], 'post_status' => 'wc-completed']);
            $progress->tick();
        }
        $progress->finish();

        self::spacer();
        self::spacer();
        self::success('Przetworzono: '.count($ids).' voucherów VIP');
        self::spacer();
        self::spacer();
    }

    public function importNonVIPVouchers()
    {
        $search = new process();
        $search->setDefaultQuertAttrs();
        $search->setVIPfilter('!=');
        $search->setDateQueryAttrs('>');
        $ids = $search->getOrdersIDsToProcess();
        $a = new aeroVoucher();
        $progress = WP_CLI\Utils\make_progress_bar('Importowanie voucherów non VIP: ', count($ids));
        self::spacer();
        self::spacer();
        for ($i = 0; $i < count($ids); ++$i) {
            $a->generateVoucher($ids[$i], true);
            wp_update_post(['ID' => $ids[$i], 'post_status' => 'wc-completed']);
            $progress->tick();
        }
        $progress->finish();
        self::spacer();
        self::spacer();
        self::success('Przetworzono: '.count($ids).' voucherów non VIP');
        self::spacer();
        self::spacer();
    }

    private static function log()
    {
        return new jdLog();
    }
}
