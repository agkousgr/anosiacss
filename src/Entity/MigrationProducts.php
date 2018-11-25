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
    private $oldId;

    /**
     * @var int|null
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
     * @var string|null
     */
    private $smallDescription;

    /**
     * @var string|null
     */
    private $largeDescription;

    /**
     * @var string|null
     */
    private $ingredients;

    /**
     * @var string|null
     */
    private $instructions;

    /**
     * @var string|null
     */
    private $oldSlug;

    /**
     * @var string|null
     */
    private $slug;

    /**
     * @var string|null
     */
    private $seoTitle;

    /**
     * @var string|null
     */
    private $seoKeywords;

    /**
     * @var string|null
     */
    private $manufacturer;

    /**
     * @var string|null
     */
    private $images;

    /**
     * @var string|null
     */
    private $imageUpdateError;

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
    public function getOldId(): int
    {
        return $this->oldId;
    }

    /**
     * @param int $oldId
     * @return MigrationProducts
     */
    public function setOldId(int $oldId): MigrationProducts
    {
        $this->oldId = $oldId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getS1id(): ?int
    {
        return $this->s1id;
    }

    /**
     * @param int|null $s1id
     * @return MigrationProducts
     */
    public function setS1id(?int $s1id): MigrationProducts
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
     * @return null|string
     */
    public function getSmallDescription(): ?string
    {
        return $this->smallDescription;
    }

    /**
     * @param null|string $smallDescription
     * @return MigrationProducts
     */
    public function setSmallDescription(?string $smallDescription): MigrationProducts
    {
        $this->smallDescription = $smallDescription;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLargeDescription(): ?string
    {
        return $this->largeDescription;
    }

    /**
     * @param null|string $largeDescription
     * @return MigrationProducts
     */
    public function setLargeDescription(?string $largeDescription): MigrationProducts
    {
        $this->largeDescription = $largeDescription;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    /**
     * @param null|string $ingredients
     * @return MigrationProducts
     */
    public function setIngredients(?string $ingredients): MigrationProducts
    {
        $this->ingredients = $ingredients;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    /**
     * @param null|string $instructions
     * @return MigrationProducts
     */
    public function setInstructions(?string $instructions): MigrationProducts
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getOldSlug(): ?string
    {
        return $this->oldSlug;
    }

    /**
     * @param null|string $oldSlug
     * @return MigrationProducts
     */
    public function setOldSlug(?string $oldSlug): MigrationProducts
    {
        $this->oldSlug = $oldSlug;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param null|string $slug
     * @return MigrationProducts
     */
    public function setSlug(?string $slug): MigrationProducts
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    /**
     * @param null|string $seoTitle
     * @return MigrationProducts
     */
    public function setSeoTitle(?string $seoTitle): MigrationProducts
    {
        $this->seoTitle = $seoTitle;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSeoKeywords(): ?string
    {
        return $this->seoKeywords;
    }

    /**
     * @param null|string $seoKeywords
     * @return MigrationProducts
     */
    public function setSeoKeywords(?string $seoKeywords): MigrationProducts
    {
        $this->seoKeywords = $seoKeywords;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * @param null|string $manufacturer
     * @return MigrationProducts
     */
    public function setManufacturer(?string $manufacturer): MigrationProducts
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

    /**
     * @return null|string
     */
    public function getImageUpdateError(): ?string
    {
        return $this->imageUpdateError;
    }

    /**
     * @param null|string $imageUpdateError
     * @return MigrationProducts
     */
    public function setImageUpdateError(?string $imageUpdateError): MigrationProducts
    {
        $this->imageUpdateError = $imageUpdateError;
        return $this;
    }

}