<?php

namespace App\Entity;

use App\Traits\{
    BasicDbFieldsTrait, BlameableTrait, TimestampableTrait, CommonDbFieldsTrait
};
use Symfony\Component\Validator\Constraints as Assert;

class Article
{
    use BasicDbFieldsTrait, BlameableTrait, TimestampableTrait, CommonDbFieldsTrait;

    /**
     * @var string|null
     *
     */
    private $image;

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var int|null
     */
    private $views;

    /**
     * @var AdminCategory
     */
    private $category;

    /**
     * @return null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param null $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return null|string
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param null|string $summary
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
     */
    public function setViews(?int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return AdminCategory
     */
    public function getCategory(): ?AdminCategory
    {
        return $this->category;
    }

    /**
     * Set AdminCategory
     *
     * @param \App\Entity\AdminCategory $category
     *
     * @return AdminCategory
     */
    public function setCategory(?AdminCategory $category): void
    {
        $this->category = $category;
    }

}