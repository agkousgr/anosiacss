<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 */
class User implements \Serializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $username;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var bool
     */
    private $isActive = true;

    /**
     * @var \DateTime|null
     */
    private $lastLogin;

    /**
     * @var string|null
     */
    private $confirmationToken;

    /**
     * @var \DateTime|null
     */
    private $passwordRequestedAt;

    /**
     * @var int|null
     */
    private $passwordRequestCounter;

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get username.
     *
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param null|string $username
     *
     * @return User
     */
    public function setUsername(?string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get password.
     *
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param null|string $password
     *
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get email.
     *
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param null|string $email
     *
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive(bool $isActive): User
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get lastLogin.
     *
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Set lastLogin.
     *
     * @param \DateTime|null $lastLogin
     *
     * @return User
     */
    public function setLastLogin(?\DateTime $lastLogin): User
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get confirmationToken.
     *
     * @return null|string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmationToken.
     *
     * @param null|string $confirmationToken
     *
     * @return User
     */
    public function setConfirmationToken(?string $confirmationToken): User
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get passwordRequestedAt.
     *
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Set passwordRequestedAt.
     *
     * @param \DateTime|null $passwordRequestedAt
     *
     * @return User
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): User
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get passwordRequestCounter.
     *
     * @return int|null
     */
    public function getPasswordRequestCounter(): ?int
    {
        return $this->passwordRequestCounter;
    }

    /**
     * Set passwordRequestCounter.
     *
     * @param int|null $passwordRequestCounter
     *
     * @return User
     */
    public function setPasswordRequestCounter(?int $passwordRequestCounter): User
    {
        $this->passwordRequestCounter = $passwordRequestCounter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }
}
