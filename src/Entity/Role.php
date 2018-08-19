<?php

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

/**
 * Class Role
 */
class Role
{
    use BasicDbFieldsTrait;

    /**
     * @var string
     */
    private $description;

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
