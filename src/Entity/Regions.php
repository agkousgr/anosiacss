<?php

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

class Regions
{
    use BasicDbFieldsTrait;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var \App\Entity\Countries
     */
    private $country;

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return \App\Entity\Countries
     */
    public function getCountry()
    {
        return $this->country;
    }

}