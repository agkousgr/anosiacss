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
     * @var int|null
     */
    private $menuId;

    /**
     * @var string|null
     */
    private $image;

    /**
     * @var string|null
     */
    private $retailPrice;

    /**
     * @var string|null
     */
    private $webPrice;

    /**
     * @var int|null
     */
    private $discount;

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

    /**
     * @return int|null
     */
    public function getMenuId(): ?int
    {
        return $this->menuId;
    }

    /**
     * @param int|null $menuId
     * @return Products
     */
    public function setMenuId(?int $menuId): Products
    {
        $this->menuId = $menuId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return Products
     */
    public function setImage(?string $image): Products
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRetailPrice(): ?string
    {
        return $this->retailPrice;
    }

    /**
     * @param string|null $retailPrice
     * @return Products
     */
    public function setRetailPrice(?string $retailPrice): Products
    {
        $this->retailPrice = $retailPrice;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebPrice(): ?string
    {
        return $this->webPrice;
    }

    /**
     * @param string|null $webPrice
     * @return Products
     */
    public function setWebPrice(?string $webPrice): Products
    {
        $this->webPrice = $webPrice;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    /**
     * @param int|null $discount
     * @return Products
     */
    public function setDiscount(?int $discount): Products
    {
        $this->discount = $discount;
        return $this;
    }

}