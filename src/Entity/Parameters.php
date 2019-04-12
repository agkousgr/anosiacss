<?php


namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

class Parameters
{
    use BasicDbFieldsTrait;

    /**
     * @var string|null
     */
    private $s1Value;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @return string|null
     */
    public function getS1Value(): ?string
    {
        return $this->s1Value;
    }

    /**
     * @param string|null $s1Value
     * @return Parameters
     */
    public function setS1Value(?string $s1Value): Parameters
    {
        $this->s1Value = $s1Value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Parameters
     */
    public function setDescription(?string $description): Parameters
    {
        $this->description = $description;
        return $this;
    }

}