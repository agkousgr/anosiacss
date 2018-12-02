<?php


namespace App\Entity;


class Products
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $slug;

    /**
     * @var string|null
     */
    private $prCode;

    /**
     * @var string|null
     */
    private $barcode;

    /**
     * @var string|null
     */
    private $productName;

    /**
     * @var int|null
     */
    private $views;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Products
     */
    public function setId(int $id): Products
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return Products
     */
    public function setSlug(?string $slug): Products
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrCode(): ?string
    {
        return $this->prCode;
    }

    /**
     * @param string|null $prCode
     * @return Products
     */
    public function setPrCode(?string $prCode): Products
    {
        $this->prCode = $prCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    /**
     * @param string|null $barcode
     * @return Products
     */
    public function setBarcode(?string $barcode): Products
    {
        $this->barcode = $barcode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductName(): ?string
    {
        return $this->productName;
    }

    /**
     * @param string|null $productName
     * @return Products
     */
    public function setProductName(?string $productName): Products
    {
        $this->productName = $productName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getViews(): ?int
    {
        return $this->views;
    }

    /**
     * @param int|null $views
     * @return Products
     */
    public function setViews(?int $views): Products
    {
        $this->views = $views;
        return $this;
    }

}