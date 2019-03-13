<?php

namespace App\Entity;

use App\Traits\BasicDbFieldsTrait;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * Class Category
 * @package App\Entity
 */
class Category
{
    use BasicDbFieldsTrait;

    /**
     * @var int|null
     */
    private $s1id;

    /**
     * @var int|null
     */
    private $s1Level;

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
    private $itemsCount = 0;

    /**
     * @var string|null
     */
    private $imageUrl;

    /**
     * @var bool
     */
    private $isVisible = true;

    /**
     * @var int|null
     */
    private $priority;

    /**
     * @var string|null
     */
    private $alternativeCategories;

    /**
     * @var Collection
     */
    private $parents;

    /**
     * @var Collection
     */
    private $children;
    
    /**
     * @var Collection
     */
    private $slides;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->slides = new ArrayCollection();
    }

    /**
     * Get s1id.
     *
     * @return int|null
     */
    public function getS1id(): ?int
    {
        return $this->s1id;
    }

    /**
     * Set s1id.
     *
     * @param int|null $s1id
     *
     * @return Category
     */
    public function setS1id(?int $s1id): Category
    {
        $this->s1id = $s1id;

        return $this;
    }

    /**
     * Get s1Level.
     *
     * @return int|null
     */
    public function getS1Level(): ?int
    {
        return $this->s1Level;
    }

    /**
     * Set s1Level.
     *
     * @param int|null $s1Level
     *
     * @return Category
     */
    public function setS1Level(?int $s1Level): Category
    {
        $this->s1Level = $s1Level;

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
     * @return Category
     */
    public function setSlug(?string $slug): Category
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Category
     */
    public function setDescription(?string $description): Category
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get itemsCount.
     *
     * @return int|null
     */
    public function getItemsCount(): ?int
    {
        return $this->itemsCount;
    }

    /**
     * Set itemsCount.
     *
     * @param int|null $itemsCount
     *
     * @return Category
     */
    public function setItemsCount(?int $itemsCount): Category
    {
        $this->itemsCount = $itemsCount;

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
     * @return Category
     */
    public function setImageUrl(?string $imageUrl): Category
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get isVisible.
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    /**
     * Set isVisible.
     *
     * @param bool $isVisible
     *
     * @return Category
     */
    public function setIsVisible(bool $isVisible): Category
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get priority.
     *
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set priority.
     *
     * @param int|null $priority
     *
     * @return Category
     */
    public function setPriority(?int $priority): Category
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get alternativeCategories.
     *
     * @return string|null
     */
    public function getAlternativeCategories(): ?string
    {
        return $this->alternativeCategories;
    }

    /**
     * Set alternativeCategories.
     *
     * @param string|null $alternativeCategories
     *
     * @return Category
     */
    public function setAlternativeCategories(?string $alternativeCategories): Category
    {
        $this->alternativeCategories = $alternativeCategories;

        return $this;
    }

    /**
     * Add parent.
     *
     * @param \App\Entity\Category $parent
     *
     * @return Category
     */
    public function addParent(Category $parent): Category
    {
        if (!$this->parents->contains($parent)) {
            $parent->addChild($this);
            $this->parents[] = $parent;
        }

        return $this;
    }

    /**
     * Remove parent.
     *
     * @param \App\Entity\Category $parent
     *
     * @return bool
     */
    public function removeParent(Category $parent): bool
    {
        return $this->parents->removeElement($parent);
    }

    /**
     * Get parents.
     *
     * @return Collection
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }
    
    /**
     * Add child.
     *
     * @param \App\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(Category $child): Category
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }

        return $this;
    }

    /**
     * Remove child.
     *
     * @param \App\Entity\Category $child
     *
     * @return bool
     */
    public function removeChild(Category $child): bool
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return Collection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    /**
     * Add slide.
     *
     * @param \App\Entity\Slider $slide
     *
     * @return Category
     */
    public function addSlide(Slider $slide): Category
    {
        $this->slides[] = $slide;

        return $this;
    }

    /**
     * Remove slide.
     *
     * @param \App\Entity\Category $slide
     *
     * @return bool
     */
    public function removeSlide(Category $slide): bool
    {
        return $this->slides->removeElement($slide);
    }

    /**
     * Get slides.
     *
     * @return Collection
     */
    public function getSlides(): Collection
    {
        return $this->slides;
    }
}
