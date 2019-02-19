<?php

namespace App\Entity;

class Checkout extends WebUser
{
    const PAYMENT_TYPES = [
        'Κατάθεση σε τράπεζα' => '1009',
        'Πληρωμή στο κατάστημα' => '1008',
        'Πιστωτική' => '1001',
        'Paypal' => '1006',
        'Αντικαταβολή' => '1007',
    ];

    const SHIPPING_TYPES = [
        'Courier' => '104',
        'Παραλαβή από το φαρμακείο' => '105',
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
     * @var double|null
     */
    private $totalOrderCost;

    /**
     * @var int|null
     */
    private $installments;

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
     * @var string|null
     */
    private $recepientName;

    /**
     * @var string|null
     */
    private $shipAddress;

    /**
     * $var string|null
     */
    private $shipZip;

    /**
     * $var string|null
     */
    private $shipCity;

    /**
     * $var string|null
     */
    private $shipDistrict;

    /**
     * $var string|null
     */
    private $couponDisc;

    /**
     * $var string|null
     */
    private $couponName;

    /**
     * $var int|null
     */
    private $couponId;

    /**
     * @var int|null
     */
    private $newsLetterAge;

    /**
     * @var string|null
     */
    private $newsLetterGender;

    /**
     * @var string|null
     */
    private $pireausResultCode;

    /**
     * @var string|null
     */
    private $pireausResultDescription;

    /**
     * @var string|null
     */
    private $pireausResultAction;

    /**
     * @var \DateTime|null
     */
    private $pireausTimestamp;

    /**
     * @var string|null
     */
    private $pireausTranTicket;

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

    /**
     * @return float|null
     */
    public function getTotalOrderCost(): ?float
    {
        return $this->totalOrderCost;
    }

    /**
     * @param float|null $totalOrderCost
     */
    public function setTotalOrderCost(?float $totalOrderCost): void
    {
        $this->totalOrderCost = $totalOrderCost;
    }

    /**
     * @return int|null
     */
    public function getInstallments(): ?int
    {
        return $this->installments;
    }

    /**
     * @param int|null $installments
     * @return Checkout
     */
    public function setInstallments(?int $installments): Checkout
    {
        $this->installments = $installments;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecepientName(): ?string
    {
        return $this->recepientName;
    }

    /**
     * @param string|null $recepientName
     * @return Checkout
     */
    public function setRecepientName(?string $recepientName): Checkout
    {
        $this->recepientName = $recepientName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getShipAddress(): ?string
    {
        return $this->shipAddress;
    }

    /**
     * @param null|string $shipAddress
     * @return Checkout
     */
    public function setShipAddress(?string $shipAddress): Checkout
    {
        $this->shipAddress = $shipAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipZip()
    {
        return $this->shipZip;
    }

    /**
     * @param mixed $shipZip
     * @return Checkout
     */
    public function setShipZip($shipZip)
    {
        $this->shipZip = $shipZip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipCity()
    {
        return $this->shipCity;
    }

    /**
     * @param mixed $shipCity
     * @return Checkout
     */
    public function setShipCity($shipCity)
    {
        $this->shipCity = $shipCity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipDistrict()
    {
        return $this->shipDistrict;
    }

    /**
     * @param mixed $shipDistrict
     * @return Checkout
     */
    public function setShipDistrict($shipDistrict)
    {
        $this->shipDistrict = $shipDistrict;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCouponDisc()
    {
        return $this->couponDisc;
    }

    /**
     * @param mixed $couponDisc
     * @return Checkout
     */
    public function setCouponDisc($couponDisc)
    {
        $this->couponDisc = $couponDisc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCouponName()
    {
        return $this->couponName;
    }

    /**
     * @param mixed $couponName
     * @return Checkout
     */
    public function setCouponName($couponName)
    {
        $this->couponName = $couponName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCouponId()
    {
        return $this->couponId;
    }

    /**
     * @param mixed $couponId
     * @return Checkout
     */
    public function setCouponId($couponId)
    {
        $this->couponId = $couponId;
        return $this;
    }

    /**
     * Get newsLetterAge.
     *
     * @return int|null
     */
    public function getNewsLetterAge(): ?int
    {
        return $this->newsLetterAge;
    }

    /**
     * Set newsLetterAge.
     *
     * @param int|null $newsLetterAge
     *
     * @return Checkout
     */
    public function setNewsLetterAge(?int $newsLetterAge): Checkout
    {
        $this->newsLetterAge = $newsLetterAge;

        return $this;
    }

    /**
     * Get newsLetterGender.
     *
     * @return string|null
     */
    public function getNewsLetterGender(): ?string
    {
        return $this->newsLetterGender;
    }

    /**
     * Set newsLetterGender.
     *
     * @param string|null $newsLetterGender
     *
     * @return Checkout
     */
    public function setNewsLetterGender(?string $newsLetterGender): Checkout
    {
        $this->newsLetterGender = $newsLetterGender;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPireausResultCode(): ?string
    {
        return $this->pireausResultCode;
    }

    /**
     * @param null|string $pireausResultCode
     * @return Checkout
     */
    public function setPireausResultCode(?string $pireausResultCode): Checkout
    {
        $this->pireausResultCode = $pireausResultCode;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPireausResultDescription(): ?string
    {
        return $this->pireausResultDescription;
    }

    /**
     * @param null|string $pireausResultDescription
     * @return Checkout
     */
    public function setPireausResultDescription(?string $pireausResultDescription): Checkout
    {
        $this->pireausResultDescription = $pireausResultDescription;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPireausResultAction(): ?string
    {
        return $this->pireausResultAction;
    }

    /**
     * @param null|string $pireausResultAction
     * @return Checkout
     */
    public function setPireausResultAction(?string $pireausResultAction): Checkout
    {
        $this->pireausResultAction = $pireausResultAction;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPireausTimestamp(): ?\DateTime
    {
        return $this->pireausTimestamp;
    }

    /**
     * @param \DateTime|null $pireausTimestamp
     * @return Checkout
     */
    public function setPireausTimestamp(?\DateTime $pireausTimestamp): Checkout
    {
        $this->pireausTimestamp = $pireausTimestamp;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPireausTranTicket(): ?string
    {
        return $this->pireausTranTicket;
    }

    /**
     * @param null|string $pireausTranTicket
     * @return Checkout
     */
    public function setPireausTranTicket(?string $pireausTranTicket): Checkout
    {
        $this->pireausTranTicket = $pireausTranTicket;
        return $this;
    }
}