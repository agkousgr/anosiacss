<?php

namespace App\Traits;

/**
 * Trait TimestampableTrait
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return object
     */
    public function setCreatedAt(?\DateTime $createdAt): object
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return object
     */
    public function setUpdatedAt(?\DateTime $updatedAt): object
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
