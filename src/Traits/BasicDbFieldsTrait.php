<?php

namespace App\Traits;

/**
 * Trait BasicDbFieldsTrait
 */
trait BasicDbFieldsTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

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
     * Get name.
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param null|string $name
     *
     * @return object
     */
    public function setName(?string $name): object
    {
        $this->name = $name;

        return $this;
    }
}
