<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 12/7/2018
 * Time: 11:28 πμ
 */

namespace App\Entity;


use App\Traits\BasicDbFieldsTrait;

class Address
{
    use BasicDbFieldsTrait;

    /**
     * @var string
     */
    protected $address;

    /**
     * $var string
     */
    protected $zip;

    /**
     * $var string
     */
    protected $city;

    /**
     * $var string
     */
    protected $district;

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
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