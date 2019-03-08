<?php


namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

class Country
{
    use BasicDbFieldsTrait;

    /**
     * @var string|null
     */
    private $intCode;

    /**
     * @var int|null
     */
    private $s1Id;

    /**
     * @return string|null
     */
    public function getIntCode(): ?string
    {
        return $this->intCode;
    }

    /**
     * @param string|null $intCode
     * @return Country
     */
    public function setIntCode(?string $intCode): Country
    {
        $this->intCode = $intCode;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getS1Id(): ?int
    {
        return $this->s1Id;
    }

    /**
     * @param int|null $s1Id
     * @return Country
     */
    public function setS1Id(?int $s1Id): Country
    {
        $this->s1Id = $s1Id;
        return $this;
    }
}