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
     * @var string|null
     */
    protected $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var bool
     */
    private $isVisible;

    /**
     * @var int
     */
    private $priority;

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
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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

}
