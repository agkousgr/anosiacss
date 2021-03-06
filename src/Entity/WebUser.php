<?php

namespace App\Entity;


class WebUser extends Address
{
    /**
     * @var string|null
     */
    protected $clientId;

    /**
     * @var string|null
     */
    protected $newsletterId;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $firstname;

    /**
     * @var string|null
     */
    protected $lastname;

    /**
     * @var bool|null
     */
    protected $newsletter;

    /**
     * @var string|null
     */
    protected $afm;

    /**
     * @var string|null
     */
    protected $irs;

    /**
     * $var string|null
     */
    protected $phone01;

    /**
     * $var string|null
     */
    protected $phone02;

    /**
     * @return null|string
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param null|string $clientId
     */
    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
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

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param null|string $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return null|string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param null|string $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return bool|null
     */
    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    /**
     * @param bool|null $newsletter
     */
    public function setNewsletter(?bool $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @return null|string
     */
    public function getAfm(): ?string
    {
        return $this->afm;
    }

    /**
     * @param null|string $afm
     */
    public function setAfm(?string $afm): void
    {
        $this->afm = $afm;
    }

    /**
     * @return null|string
     */
    public function getIrs(): ?string
    {
        return $this->irs;
    }

    /**
     * @param null|string $irs
     */
    public function setIrs(?string $irs): void
    {
        $this->irs = $irs;
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

}