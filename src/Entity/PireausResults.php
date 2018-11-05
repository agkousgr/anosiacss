<?php


namespace App\Entity;


class PireausResults
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $cliend_id;

    /**
     * @var int
     */
    private $support_reference_id;

    /**
     * @var string
     */
    private $merchant_reference;

    /**
     * @var string|null
     */
    private $status_flag;

    /**
     * @var string|null
     */
    private $response_code;

    /**
     * @var string|null
     */
    private $response_description;

    /**
     * @var string|null
     */
    private $result_code;

    /**
     * @var string|null
     */
    private $result_description;

    /**
     * @var string|null
     */
    private $approval_code;

    /**
     * @var int|null
     */
    private $package_no;

    /**
     * @var string|null
     */
    private $auth_status;

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
    public function getCliendId(): int
    {
        return $this->cliend_id;
    }

    /**
     * @param int $cliend_id
     * @return PireausResults
     */
    public function setCliendId(int $cliend_id): PireausResults
    {
        $this->cliend_id = $cliend_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSupportReferenceId(): int
    {
        return $this->support_reference_id;
    }

    /**
     * @param int $support_reference_id
     * @return PireausResults
     */
    public function setSupportReferenceId(int $support_reference_id): PireausResults
    {
        $this->support_reference_id = $support_reference_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantReference(): string
    {
        return $this->merchant_reference;
    }

    /**
     * @param string $merchant_reference
     * @return PireausResults
     */
    public function setMerchantReference(string $merchant_reference): PireausResults
    {
        $this->merchant_reference = $merchant_reference;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getStatusFlag(): ?string
    {
        return $this->status_flag;
    }

    /**
     * @param null|string $status_flag
     * @return PireausResults
     */
    public function setStatusFlag(?string $status_flag): PireausResults
    {
        $this->status_flag = $status_flag;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResponseCode(): ?string
    {
        return $this->response_code;
    }

    /**
     * @param null|string $response_code
     * @return PireausResults
     */
    public function setResponseCode(?string $response_code): PireausResults
    {
        $this->response_code = $response_code;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResponseDescription(): ?string
    {
        return $this->response_description;
    }

    /**
     * @param null|string $response_description
     * @return PireausResults
     */
    public function setResponseDescription(?string $response_description): PireausResults
    {
        $this->response_description = $response_description;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResultCode(): ?string
    {
        return $this->result_code;
    }

    /**
     * @param null|string $result_code
     * @return PireausResults
     */
    public function setResultCode(?string $result_code): PireausResults
    {
        $this->result_code = $result_code;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResultDescription(): ?string
    {
        return $this->result_description;
    }

    /**
     * @param null|string $result_description
     * @return PireausResults
     */
    public function setResultDescription(?string $result_description): PireausResults
    {
        $this->result_description = $result_description;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getApprovalCode(): ?string
    {
        return $this->approval_code;
    }

    /**
     * @param null|string $approval_code
     * @return PireausResults
     */
    public function setApprovalCode(?string $approval_code): PireausResults
    {
        $this->approval_code = $approval_code;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPackageNo(): ?int
    {
        return $this->package_no;
    }

    /**
     * @param int|null $package_no
     * @return PireausResults
     */
    public function setPackageNo(?int $package_no): PireausResults
    {
        $this->package_no = $package_no;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAuthStatus(): ?string
    {
        return $this->auth_status;
    }

    /**
     * @param null|string $auth_status
     * @return PireausResults
     */
    public function setAuthStatus(?string $auth_status): PireausResults
    {
        $this->auth_status = $auth_status;
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
     * @return PireausResults
     */
    public function setCreatedAt(\DateTime $createdAt): PireausResults
    {
        $this->createdAt = $createdAt;
        return $this;
    }






















}