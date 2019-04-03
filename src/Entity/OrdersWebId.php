<?php

namespace App\Entity;


class OrdersWebId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $orderCode;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOrderCode(): string
    {
        return $this->orderCode;
    }

    /**
     * @param string $orderCode
     * @return OrdersWebId
     */
    public function setOrderCode(int $orderCode): OrdersWebId
    {
        $this->orderCode = $orderCode;
        return $this;
    }
}