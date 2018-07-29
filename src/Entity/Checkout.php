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
    const SHIPPING_TYPES = [
        'Πληρωμή στο κατάστημα' => '1000',
        'Πιστωτική' => '1001',
        'Paypal' => '1002',
        'Αντικαταβολή' => '1003',
        'Κατάθεση σε τράπεζα' => '1004',
    ];

    const PAYMENT_TYPES = [
        'Courier' => '1000',
        'Παραλαβή από το κατάστημα' => '1001',
    ];

    /**
     * @var int
     */
    protected $nextPage;

    /**
     * 7021 For Invoice, 7023 for Receipt
     * @var string
     */
    protected $series;

    /**
     * @var string|null
     */
    protected $comments;

    /**
     * @var string
     */
    private $shippingType;

    /**
     * @var string
     */
    private $paymentType;

    /**
     * @var bool
     */
    protected $agreeTerms;

    /**
     * @return int
     */
    public function getNextPage(): int
    {
        return $this->nextPage;
    }

    /**
     * @param int $nextPage
     */
    public function setNextPage(int $nextPage): void
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @param string $series
     */
    public function setSeries(string $series): void
    {
        $this->series = $series;
    }

    /**
     * @return string
     */
    public function getShippingType(): string
    {
        return $this->shippingType;
    }

    /**
     * @param string $shippingType
     */
    public function setShippingType(string $shippingType): void
    {
        $this->shippingType = $shippingType;
    }

    /**
     * @return string
     */
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType(string $paymentType): void
    {
        $this->paymentType = $paymentType;
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
     * @return bool
     */
    public function isAgreeTerms(): bool
    {
        return $this->agreeTerms;
    }

    /**
     * @param bool $agreeTerms
     */
    public function setAgreeTerms(bool $agreeTerms): void
    {
        $this->agreeTerms = $agreeTerms;
    }
}