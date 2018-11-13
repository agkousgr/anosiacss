<?php


namespace App\Entity;


class MigrationProducts
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $s1id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $smallDescription;

    /**
     * @var string
     */
    private $largeDescription;

    /**
     * @var string
     */
    private $ingredients;

    /**
     * @var string
     */
    private $instructions;

    /**
     * @var string
     */
    private $oldSlug;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $seoTitle;

    /**
     * @var string
     */
    private $seoKeywords;

    /**
     * @var string
     */
    private $manufacturer;

    /**
     * @var string|null
     */
    private $images;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getS1id(): int
    {
        return $this->s1id;
    }

    /**
     * @param int $s1id
     * @return MigrationProducts
     */
    public function setS1id(int $s1id): MigrationProducts
    {
        $this->s1id = $s1id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return MigrationProducts
     */
    public function setName(string $name): MigrationProducts
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return MigrationProducts
     */
    public function setSku(string $sku): MigrationProducts
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getSmallDescription(): string
    {
        return $this->smallDescription;
    }

    /**
     * @param string $smallDescription
     * @return MigrationProducts
     */
    public function setSmallDescription(string $smallDescription): MigrationProducts
    {
        $this->smallDescription = $smallDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getLargeDescription(): string
    {
        return $this->largeDescription;
    }

    /**
     * @param string $largeDescription
     * @return MigrationProducts
     */
    public function setLargeDescription(string $largeDescription): MigrationProducts
    {
        $this->largeDescription = $largeDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getIngredients(): string
    {
        return $this->ingredients;
    }

    /**
     * @param string $ingredients
     * @return MigrationProducts
     */
    public function setIngredients(string $ingredients): MigrationProducts
    {
        $this->ingredients = $ingredients;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return $this->instructions;
    }

    /**
     * @param string $instructions
     * @return MigrationProducts
     */
    public function setInstructions(string $instructions): MigrationProducts
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * @return string
     */
    public function getOldSlug(): string
    {
        return $this->oldSlug;
    }

    /**
     * @param string $oldSlug
     * @return MigrationProducts
     */
    public function setOldSlug(string $oldSlug): MigrationProducts
    {
        $this->oldSlug = $oldSlug;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return MigrationProducts
     */
    public function setSlug(string $slug): MigrationProducts
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    /**
     * @param string $seoTitle
     * @return MigrationProducts
     */
    public function setSeoTitle(string $seoTitle): MigrationProducts
    {
        $this->seoTitle = $seoTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeoKeywords(): string
    {
        return $this->seoKeywords;
    }

    /**
     * @param string $seoKeywords
     * @return MigrationProducts
     */
    public function setSeoKeywords(string $seoKeywords): MigrationProducts
    {
        $this->seoKeywords = $seoKeywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    /**
     * @param string $manufacturer
     * @return MigrationProducts
     */
    public function setManufacturer(string $manufacturer): MigrationProducts
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImages(): ?string
    {
        return $this->images;
    }

    /**
     * @param null|string $images
     * @return MigrationProducts
     */
    public function setImages(?string $images): MigrationProducts
    {
        $this->images = $images;
        return $this;
    }

}