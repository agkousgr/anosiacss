<?php


namespace App\Entity;


class Manufacturer
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var int|null
     */
    private $code;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Manufacturer
     */
    public function setId(?int $id): Manufacturer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @param int|null $code
     * @return Manufacturer
     */
    public function setCode(?int $code): Manufacturer
    {
        $this->code = $code;
        return $this;
    }
}