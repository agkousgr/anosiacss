<?php


namespace App\Entity;


class PireausTransaction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var int
     */
    private $supportReferenceId;

    /**
     * @var string
     */
    private $merchantReference;

    /**
     * @var string|null
     */
    private $statusFlag;

    /**
     * @var string|null
     */
    private $responseCode;

    /**
     * @var string|null
     */
    private $responseDescription;

    /**
     * @var string|null
     */
    private $resultCode;

    /**
     * @var string|null
     */
    private $resultDescription;

    /**
     * @var string|null
     */
    private $approvalCode;

    /**
     * @var int|null
     */
    private $packageNo;

    /**
     * @var string|null
     */
    private $authStatus;

    /**
     * @var \DateTime
     */
    private $createdAt;

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
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     * @return PireausTransaction
     */
    public function setClientId(int $clientId): PireausTransaction
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSupportReferenceId(): int
    {
        return $this->supportReferenceId;
    }

    /**
     * @param int $supportReferenceId
     * @return PireausTransaction
     */
    public function setSupportReferenceId(int $supportReferenceId): PireausTransaction
    {
        $this->supportReferenceId = $supportReferenceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }

    /**
     * @param string $merchantReference
     * @return PireausTransaction
     */
    public function setMerchantReference(string $merchantReference): PireausTransaction
    {
        $this->merchantReference = $merchantReference;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatusFlag(): ?string
    {
        return $this->statusFlag;
    }

    /**
     * @param null|string $statusFlag
     * @return PireausTransaction
     */
    public function setStatusFlag(?string $statusFlag): PireausTransaction
    {
        $this->statusFlag = $statusFlag;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }

    /**
     * @param null|string $responseCode
     * @return PireausTransaction
     */
    public function setResponseCode(?string $responseCode): PireausTransaction
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResponseDescription(): ?string
    {
        return $this->responseDescription;
    }

    /**
     * @param null|string $responseDescription
     * @return PireausTransaction
     */
    public function setResponseDescription(?string $responseDescription): PireausTransaction
    {
        $this->responseDescription = $responseDescription;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResultCode(): ?string
    {
        return $this->resultCode;
    }

    /**
     * @param null|string $resultCode
     * @return PireausTransaction
     */
    public function setResultCode(?string $resultCode): PireausTransaction
    {
        $this->resultCode = $resultCode;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResultDescription(): ?string
    {
        return $this->resultDescription;
    }

    /**
     * @param null|string $resultDescription
     * @return PireausTransaction
     */
    public function setResultDescription(?string $resultDescription): PireausTransaction
    {
        $this->resultDescription = $resultDescription;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getApprovalCode(): ?string
    {
        return $this->approvalCode;
    }

    /**
     * @param null|string $approvalCode
     * @return PireausTransaction
     */
    public function setApprovalCode(?string $approvalCode): PireausTransaction
    {
        $this->approvalCode = $approvalCode;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPackageNo(): ?int
    {
        return $this->packageNo;
    }

    /**
     * @param int|null $packageNo
     * @return PireausTransaction
     */
    public function setPackageNo(?int $packageNo): PireausTransaction
    {
        $this->packageNo = $packageNo;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthStatus(): ?string
    {
        return $this->authStatus;
    }

    /**
     * @param null|string $authStatus
     * @return PireausTransaction
     */
    public function setAuthStatus(?string $authStatus): PireausTransaction
    {
        $this->authStatus = $authStatus;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return PireausTransaction
     */
    public function setCreatedAt(\DateTime $createdAt): PireausTransaction
    {
        $this->createdAt = $createdAt;
        return $this;
    }






















}