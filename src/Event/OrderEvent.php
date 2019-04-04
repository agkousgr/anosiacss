<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Checkout;

/**
 * Class OrderEvent
 */
class OrderEvent extends Event
{
    public const ORDER_COURIER = 'order.courier';
    public const ORDER_TAKE_AWAY = 'order.take_away';
    public const ORDER_VOUCHER = 'order.voucher';
    public const ORDER_AFTER_SALES = 'order.after_sales';

    /**
     * @var Checkout
     */
    private $order;

    /**
     * Constructor.
     *
     * @param \App\Entity\Checkout $order
     */
    public function __construct(Checkout $order)
    {
        $this->order = $order;
    }

    /**
     * Get order.
     *
     * @return \App\Entity\Checkout
     */
    public function getOrder(): Checkout
    {
        return $this->order;
    }
}
