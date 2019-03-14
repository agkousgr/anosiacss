<?php


namespace App\Entity;


class S1AllProductsIds
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
     * @var int|null
     */
    private $mnf;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return S1AllProductsIds
     */
    public function setId(?int $id): S1AllProductsIds
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
     * @return S1AllProductsIds
     */
    public function setCode(?int $code): S1AllProductsIds
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMnf(): ?int
    {
        return $this->mnf;
    }

    /**
     * @param int|null $mnf
     * @return S1AllProductsIds
     */
    public function setMnf(?int $mnf): S1AllProductsIds
    {
        $this->mnf = $mnf;
        return $this;
    }

}