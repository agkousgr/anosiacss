<?php


namespace App\Entity;


class Product
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
     * @var \DateTime|null
     */
    private $latestOffer;

    /**
     * @var bool|null
     */
    private $webVisible;

    /**
     * @var bool|null
     */
    private $active;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function setId(int $id): Product
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
     * @return Product
     */
    public function setSlug(?string $slug): Product
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
     * @return Product
     */
    public function setPrCode(?string $prCode): Product
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
     * @return Product
     */
    public function setBarcode(?string $barcode): Product
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
     * @return Product
     */
    public function setProductName(?string $productName): Product
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
     * @return Product
     */
    public function setViews(?int $views): Product
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
     * @return Product
     */
    public function setMenuId(?int $menuId): Product
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
     * @return Product
     */
    public function setImage(?string $image): Product
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
     * @return Product
     */
    public function setRetailPrice(?string $retailPrice): Product
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
     * @return Product
     */
    public function setWebPrice(?string $webPrice): Product
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
     * @return Product
     */
    public function setDiscount(?int $discount): Product
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLatestOffer(): ?\DateTime
    {
        return $this->latestOffer;
    }

    /**
     * @param \DateTime|null $latestOffer
     * @return Product
     */
    public function setLatestOffer(?\DateTime $latestOffer): Product
    {
        $this->latestOffer = $latestOffer;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isWebVisible(): ?bool
    {
        return $this->webVisible;
    }

    /**
     * @param bool|null $webVisible
     * @return Product
     */
    public function setWebVisible(?bool $webVisible): Product
    {
        $this->webVisible = $webVisible;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     * @return Product
     */
    public function setActive(?bool $active): Product
    {
        $this->active = $active;
        return $this;
    }

}