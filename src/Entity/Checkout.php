<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 22/7/2018
 * Time: 2:53 μμ
 */

namespace App\Entity;


class Checkout extends WebUser
{
    const PAYMENT_TYPES = [
        'Κατάθεση σε τράπεζα' => '1004',
        'Πληρωμή στο κατάστημα' => '1000',
        'Πιστωτική' => '1001',
        'Paypal' => '1002',
        'Αντικαταβολή' => '1003',
    ];

    const SHIPPING_TYPES = [
        'Courier' => '1000',
        'Παραλαβή από το φαρμακείο' => '1001',
    ];

    /**
     * @var int|null
     */
    private $nextPage;

    /**
     * @var double|null
     *
     */
    private $shippingCost;

    /**
     * @var double|null
     */
    private $antikatavoliCost;

    /**
     * 7021 For Invoice, 7023 for Receipt
     * @var string|null
     */
    private $series;

    /**
     * @var string|null
     */
    private $comments;

    /**
     * @var string|null
     */
    private $shippingType;

    /**
     * @var string|null
     */
    private $paymentType;

    /**
     * @var bool|null
     */
    private $agreeTerms;

    /**
     * $var int|null
     */
    private $orderNo;

    /**
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @param int|null $nextPage
     */
    public function setNextPage(?int $nextPage): void
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return null|string
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string $series
     */
    public function setSeries(?string $series): void
    {
        $this->series = $series;
    }

    /**
     * @return null|string
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param null|string $comments
     */
    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return string
     */
    public function getShippingType(): ?string
    {
        return $this->shippingType;
    }

    /**
     * @param string $shippingType
     */
    public function setShippingType(?string $shippingType): void
    {
        $this->shippingType = $shippingType;
    }

    /**
     * @return string
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType(?string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return bool|null
     */
    public function getAgreeTerms(): ?bool
    {
        return $this->agreeTerms;
    }

    /**
     * @param bool|null $agreeTerms
     */
    public function setAgreeTerms(?bool $agreeTerms): void
    {
        $this->agreeTerms = $agreeTerms;
    }

    /**
     * @return mixed
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @param mixed $orderNo
     */
    public function setOrderNo($orderNo): void
    {
        $this->orderNo = $orderNo;
    }

    /**
     * @return float|null
     */
    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    /**
     * @param float|null $shippingCost
     */
    public function setShippingCost(?float $shippingCost): void
    {
        $this->shippingCost = $shippingCost;
    }

    /**
     * @return float|null
     */
    public function getAntikatavoliCost(): ?float
    {
        return $this->antikatavoliCost;
    }

    /**
     * @param float|null $antikatavoliCost
     */
    public function setAntikatavoliCost(?float $antikatavoliCost): void
    {
        $this->antikatavoliCost = $antikatavoliCost;
    }

}