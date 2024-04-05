<?php

namespace JDCustom\voucher;

class voucherInst
{
    private int $vid;
    private int $status;

    public function __construct($id)
    {
        $this->vid = $id;
        $this->status = (int) get_post_meta($this->vid, 'vStatus', true);
        //	$this->post = get_post($this->vid);
    }

    public function getMeta($meta)
    {
        return get_post_meta($this->vid, $meta, true);
    }

    public function getOrderLink(): string
    {
        $order = wc_get_order($this->getMeta('order_id'));
        $url = $order->get_edit_order_url();

        return '<a href="'.$url.'">'.$this->getMeta('order_id').' </a>';
    }

    public function renderBool($val): void
    {
        echo match ($val) {
            0 => 'Nie',
            1 => 'Tak',
            default => 'Nieznany',
        };
    }

    /**
     * @param int $code - status code to render (0-4)
     *
     * 0- Nieaktywny
     * 1- Wysłany
     * 2- Zarezerwowany
     * 3- Wykorzystany
     * 4- Anulowany
     */
    public function renderStatus(): void
    {
        echo match ($this->status) {
            0 => 'Nieaktywny',
            1 => 'Wysłany',
            2 => 'Zarezerwowany',
            3 => 'Wykorzystany',
            4 => 'Anulowany',
            default => 'Nieznany',
        };
    }

    public function renderDate($date): void
    {
        if (!$date) {
            echo '----';
        } else {
            echo $date;
        }
    }
}
