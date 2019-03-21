<?php

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

/**
 * Class CategoryTopSeller
 */
class CategoryTopSeller
{
    use BasicDbFieldsTrait;

    /**
     * @var int|null
     */
    private $softOneId;

    /**
     * @var string|null
     */
    private $slug;

    /**
     * @var string|null
     */
    private $imageUrl;

    /**
     * @var \App\Entity\Category|null
     */
    private $category;

    /**
     * Get softOneId.
     *
     * @return int|null
     */
    public function getSoftOneId(): ?int
    {
        return $this->softOneId;
    }

    /**
     * Set softOneId.
     *
     * @param int|null $softOneId
     *
     * @return CategoryTopSeller
     */
    public function setSoftOneId(?int $softOneId): CategoryTopSeller
    {
        $this->softOneId = $softOneId;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug.
     *
     * @param string|null $slug
     *
     * @return CategoryTopSeller
     */
    public function setSlug(?string $slug): CategoryTopSeller
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get imageUrl.
     *
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * Set imageUrl.
     *
     * @param string|null $imageUrl
     *
     * @return CategoryTopSeller
     */
    public function setImageUrl(?string $imageUrl): CategoryTopSeller
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get category.
     *
     * @return \App\Entity\Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set category.
     *
     * @param \App\Entity\Category|null $category
     *
     * @return CategoryTopSeller
     */
    public function setCategory(?Category $category): CategoryTopSeller
    {
        $this->category = $category;

        return $this;
    }
}
