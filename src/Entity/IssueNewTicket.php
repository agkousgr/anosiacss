<?php

namespace App\Entity;


class IssueNewTicket
{
    /**
     * @var int
     */
    private $AcquirerId;

    /**
     * @var int
     */
    private $MerchantID;

    /**
     * @var int
     */
    private $PosID;

    /**
     * @var string
     */
    private $UserName;

    /**
     * @var string
     */
    private $Password;

    /**
     * @var string
     */
    private $RequestType;

    /**
     * @var int
     */
    private $CurrencyCode;

    /**
     * @var string
     */
    private $MerchantReference;

    /**
     * @var double
     */
    private $Amount;

    /**
     * @var int
     */
    private $Installments;

    /**
     * @var int
     */
    private $ExpirePreauth;

    /**
     * @var int
     */
    private $Bnpl;

    /**
     * @var string
     */
    private $Parameters;

    /**
     * @return int
     */
    public function getAcquirerId(): int
    {
        return $this->AcquirerId;
    }

    /**
     * @param int $AcquirerId
     * @return IssueNewTicket
     */
    public function setAcquirerId(int $AcquirerId): IssueNewTicket
    {
        $this->AcquirerId = $AcquirerId;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantID(): int
    {
        return $this->MerchantID;
    }

    /**
     * @param int $MerchantID
     * @return IssueNewTicket
     */
    public function setMerchantID(int $MerchantID): IssueNewTicket
    {
        $this->MerchantID = $MerchantID;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosID(): int
    {
        return $this->PosID;
    }

    /**
     * @param int $PosID
     * @return IssueNewTicket
     */
    public function setPosID(int $PosID): IssueNewTicket
    {
        $this->PosID = $PosID;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->UserName;
    }

    /**
     * @param string $UserName
     * @return IssueNewTicket
     */
    public function setUserName(string $UserName): IssueNewTicket
    {
        $this->UserName = $UserName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->Password;
    }

    /**
     * @param string $Password
     * @return IssueNewTicket
     */
    public function setPassword(string $Password): IssueNewTicket
    {
        $this->Password = $Password;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestType(): string
    {
        return $this->RequestType;
    }

    /**
     * @param string $RequestType
     * @return IssueNewTicket
     */
    public function setRequestType(string $RequestType): IssueNewTicket
    {
        $this->RequestType = $RequestType;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrencyCode(): int
    {
        return $this->CurrencyCode;
    }

    /**
     * @param int $CurrencyCode
     * @return IssueNewTicket
     */
    public function setCurrencyCode(int $CurrencyCode): IssueNewTicket
    {
        $this->CurrencyCode = $CurrencyCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReference(): string
    {
        return $this->MerchantReference;
    }

    /**
     * @param string $MerchantReference
     * @return IssueNewTicket
     */
    public function setMerchantReference(string $MerchantReference): IssueNewTicket
    {
        $this->MerchantReference = $MerchantReference;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->Amount;
    }

    /**
     * @param float $Amount
     * @return IssueNewTicket
     */
    public function setAmount(float $Amount): IssueNewTicket
    {
        $this->Amount = $Amount;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstallments(): int
    {
        return $this->Installments;
    }

    /**
     * @param int $Installments
     * @return IssueNewTicket
     */
    public function setInstallments(int $Installments): IssueNewTicket
    {
        $this->Installments = $Installments;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpirePreauth(): int
    {
        return $this->ExpirePreauth;
    }

    /**
     * @param int $ExpirePreauth
     * @return IssueNewTicket
     */
    public function setExpirePreauth(int $ExpirePreauth): IssueNewTicket
    {
        $this->ExpirePreauth = $ExpirePreauth;
        return $this;
    }

    /**
     * @return int
     */
    public function getBnpl(): int
    {
        return $this->Bnpl;
    }

    /**
     * @param int $Bnpl
     * @return IssueNewTicket
     */
    public function setBnpl(int $Bnpl): IssueNewTicket
    {
        $this->Bnpl = $Bnpl;
        return $this;
    }

    /**
     * @return string
     */
    public function getParameters(): string
    {
        return $this->Parameters;
    }

    /**
     * @param string $Parameters
     * @return IssueNewTicket
     */
    public function setParameters(string $Parameters): IssueNewTicket
    {
        $this->Parameters = $Parameters;
        return $this;
    }
}