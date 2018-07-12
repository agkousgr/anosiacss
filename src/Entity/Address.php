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
     * $var string
     */
    protected $phone01;

    /**
     * $var string
     */
    protected $phone02;

    /**
     * $var string
     */
    protected $email;

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

    /**
     * @return mixed
     */
    public function getPhone01()
    {
        return $this->phone01;
    }

    /**
     * @param mixed $phone01
     */
    public function setPhone01($phone01): void
    {
        $this->phone01 = $phone01;
    }

    /**
     * @return mixed
     */
    public function getPhone02()
    {
        return $this->phone02;
    }

    /**
     * @param mixed $phone02
     */
    public function setPhone02($phone02): void
    {
        $this->phone02 = $phone02;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }


}