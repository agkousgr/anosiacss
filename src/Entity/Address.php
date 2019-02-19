<?php

namespace App\Entity;


use App\Traits\BasicDbFieldsTrait;

class Address
{
    use BasicDbFieldsTrait;

    /**
     * @var int|null
     */
    protected $client;

    /**
     * @var string|null
     */
    protected $address;

    /**
     * $var string|null
     */
    protected $zip;

    /**
     * $var string|null
     */
    protected $city;

    /**
     * $var string|null
     */
    protected $district;

    /**
     * @return int|null
     */
    public function getClient(): ?int
    {
        return $this->client;
    }

    /**
     * @param int|null $client
     */
    public function setClient(?int $client): void
    {
        $this->client = $client;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district): void
    {
        $this->district = $district;
    }

}