<?php

namespace JDCustom\order;

class process
{
    private array $attrs = [];
    private string $return;
    private int $limit;
    private array $status;

    public function __construct()
    {
        $this->limit = -1;
        $this->status = ['wc-processing', 'wc-pending'];
        $this->return = 'ids';
        $this->setDefaultQuertAttrs();
    }

    /**
     * @param string $date   Date to calculate default "-2 years"
     * @param string $format DataTime format
     *
     * @return string calculated date
     *
     * @throws \Exception
     */
    public static function getExpirationDate(?string $date = '-2 years', string $format = 'd-m-Y'): string
    {
        return (new \DateTime($date))->format($format);
    }

    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @return array $attrs limit => -1, status = array('wc-processing', 'wc-pending'), return => 'ids'
     */
    public function setDefaultQuertAttrs(): array
    {
        $this->attrs['limit'] = $this->limit;
        $this->attrs['status'] = $this->status;
        $this->attrs['return'] = $this->return;
        $this->attrs['type'] = 'shop_order';

        return $this->attrs;
    }

    /**
     * @param $operator string default "<"
     * @param $date     string default "-2 years"
     * @param $format   string default "Y-m-d"
     *
     * @return array $attrs
     */
    public function setDateQueryAttrs($operator = '>', $date = null, $format = 'Y-m-d'): array
    {
        $date = $date ?: '-2 years';
        $newDate = self::getExpirationDate($date, $format);
        $operator = $operator ?: '<';
        $this->attrs['date_created'] = $operator.' '.$newDate;

        return $this->attrs;
    }

    public function setVIPfilter($compare = '=')
    {
        $compare = $compare ?: '!=';
        $this->attrs['meta_key'] = 'has_vip';
        $this->attrs['meta_compare'] = $compare;
        $this->attrs['meta_value'] = true;

        return $this->attrs;
    }

    public function setLimit(int $limit): array
    {
        $this->attrs['limit'] = $limit;

        return $this->attrs;
    }

    public function setReturn(string $return): array
    {
        $this->attrs['return'] = $return;

        return $this->attrs;
    }

    public function setOrderStatus(array $status): array
    {
        $this->attrs['status'] = $status;

        return $this->attrs;
    }

    public function getOrdersIDsToProcess()
    {
        return wc_get_orders($this->attrs);
    }
}
