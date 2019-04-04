<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\OrderEvent;
use App\Service\Mail\OrderMailer;

/**
 * Class OrderEventSubscriber
 */
class OrderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var OrderMailer
     */
    private $mailer;

    /**
     * Constructor.
     *
     * @param \App\Service\Mail\OrderMailer $mailer
     */
    public function __construct(OrderMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            OrderEvent::ORDER_COURIER => 'onCourier',
            OrderEvent::ORDER_TAKE_AWAY => 'onTakeAway',
            OrderEvent::ORDER_VOUCHER => 'onVoucher',
            OrderEvent::ORDER_AFTER_SALES => 'onAfterSales',
        ];
    }

    /**
     * Handle actions to be taken, when an order that will be delivered by courier is placed.
     *
     * @param \App\Event\OrderEvent $event
     */
    public function onCourier(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->mailer->buildAndSendCourier($order);
    }

    /**
     * Handle actions to be taken, when an order that will be received from store is placed.
     *
     * @param \App\Event\OrderEvent $event
     */
    public function onTakeAway(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->mailer->buildAndSendTakeAway($order);
    }

    /**
     * Handle actions to be taken, when an order is ready to be delivered.
     *
     * @param \App\Event\OrderEvent $event
     */
    public function onVoucher(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->mailer->buildAndSendVoucher($order);
    }

    /**
     * Handle actions to be taken, after an order has been delivered.
     *
     * @param \App\Event\OrderEvent $event
     */
    public function onAfterSales(OrderEvent $event)
    {
        $order = $event->getOrder();
        $this->mailer->buildAndSendAfterSales($order);
    }
}
