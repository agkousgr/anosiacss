<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 20/5/2018
 * Time: 2:39 μμ
 */

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class Countries
{
    use BasicDbFieldsTrait;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var ArrayCollection
     */
    private $regions;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $slug
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Get regions
     *
     * @return Collection
     */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

}