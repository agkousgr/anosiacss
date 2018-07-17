<?php

namespace App\Entity;


class WebUser
{
    /**
     * @var string|null
     */
    private $clientId;

    /**
     * @var string|null
     */
    private $newsletterId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var bool
     */
    private $newsletter;

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
     * $var string|null
     */
    protected $phone01;

    /**
     * $var string|null
     */
    protected $phone02;

    /**
     * @return string|null
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
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientId
     */
    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return bool
     */
    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }

    /**
     * @param bool $newsletter
     */
    public function setNewsletter(bool $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return null|string
     */
    public function getNewsletterId(): ?string
    {
        return $this->newsletterId;
    }

    /**
     * @param null|string $newsletterId
     */
    public function setNewsletterId(?string $newsletterId): void
    {
        $this->newsletterId = $newsletterId;
    }


}