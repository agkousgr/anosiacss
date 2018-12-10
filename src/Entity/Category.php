<?php

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;

/**
 * Class Category
 * @package App\Entity
 */
class Category
{
    use BasicDbFieldsTrait;

    /**
     * @var int
     */
    private $s1id;

    /**
     * @var string|null
     */
    private $slug;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var int|null
     */
    private $itemsCount;

    /**
     * @var string|null
     */
    private $imageUrl;

    /**
     * @var bool
     */
    private $isVisible;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string|null
     */
    private $relevantCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \App\Entity\Category
     */
    private $parent;

    /**
     * @var int|null
     */
    private $lft;

    /**
     * @var int|null
     */
    private $lvl;

    /**
     * @var int|null
     */
    private $rgt;

    /**
     * @var \App\Entity\Category
     */
    private $root;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $slides;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->slides = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setS1id(int $s1id): void
    {
        $this->s1id = $s1id;
    }

    /**
     * Add child
     *
     * @param \App\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\App\Entity\Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \App\Entity\Category $child
     */
    public function removeChild(\App\Entity\Category $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add slide
     *
     * @param \App\Entity\Category $slide
     *
     * @return Category
     */
    public function addSlide(\App\Entity\Slider $slide)
    {
        $this->slides[] = $slide;

        return $this;
    }

    /**
     * Remove slide
     *
     * @param \App\Entity\Category $slide
     */
    public function removeSlide(\App\Entity\Category $slide)
    {
        $this->slides->removeElement($slide);
    }

    /**
     * Get slides
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * Set parent
     *
     * @param \App\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(?\App\Entity\Category $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \App\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int|null
     */
    public function getLft(): ?int
    {
        return $this->lft;
    }

    /**
     * @param int|null $lft
     */
    public function setLft(?int $lft): void
    {
        $this->lft = $lft;
    }

    /**
     * @return int|null
     */
    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    /**
     * @param int|null $lvl
     */
    public function setLvl(?int $lvl): void
    {
        $this->lvl = $lvl;
    }

    /**
     * @return int|null
     */
    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    /**
     * @param int|null $rgt
     */
    public function setRgt(?int $rgt): void
    {
        $this->rgt = $rgt;
    }

    /**
     * @return Category
     */
    public function getRoot(): Category
    {
        return $this->root;
    }

    /**
     * Set root
     *
     * @param \App\Entity\Category $parent
     *
     * @return Category
     */
    public function setRoot(Category $root): void
    {
        $this->root = $root;
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
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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
    public function getItemsCount(): ?int
    {
        return $this->itemsCount;
    }

    /**
     * @param int|null $itemsCount
     * @return Category
     */
    public function setItemsCount(?int $itemsCount): Category
    {
        $this->itemsCount = $itemsCount;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param null|string $imageUrl
     */
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible
     */
    public function setIsVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return string|null
     */
    public function getRelevantCategories(): ?string
    {
        return $this->relevantCategories;
    }

    /**
     * @param string|null $relevantCategories
     * @return Category
     */
    public function setRelevantCategories(?string $relevantCategories): Category
    {
        $this->relevantCategories = $relevantCategories;
        return $this;
    }

}
