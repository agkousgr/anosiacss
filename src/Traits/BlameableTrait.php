<?php

namespace App\Traits;

/**
 * Trait BlameableTrait
 */
trait BlameableTrait
{
    /**
     * @var \App\Entity\User|null
     */
    protected $createdBy;

    /**
     * @var \App\Entity\User|null
     */
    protected $updatedBy;

    /**
     * Get createdBy.
     *
     * @return \App\Entity\User|null
     */
    public function getCreatedBy(): ?\App\Entity\User
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy.
     *
     * @param \App\Entity\User|null $createdBy
     *
     * @return object
     */
    public function setCreatedBy(?\App\Entity\User $createdBy): object
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return \App\Entity\User|null
     */
    public function getUpdatedBy(): ?\App\Entity\User
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedBy.
     *
     * @param \App\Entity\User|null $updatedBy
     *
     * @return object
     */
    public function setUpdatedBy(?\App\Entity\User $updatedBy): object 
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
