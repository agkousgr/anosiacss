<?php

namespace App\Entity;


class OrdersWebId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $orderNumber;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    /**
     * @param int $orderNumber
     * @return OrdersWebId
     */
    public function setOrderNumber(int $orderNumber): OrdersWebId
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }
}