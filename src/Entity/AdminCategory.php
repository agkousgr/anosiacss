<?php

namespace App\Entity;

use App\Traits\{BasicDbFieldsTrait, CommonDbFieldsTrait, TimestampableTrait, BlameableTrait};

class AdminCategory
{
    use BasicDbFieldsTrait, CommonDbFieldsTrait, TimestampableTrait, BlameableTrait;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var int|null
     */
    private $left;

    /**
     * @var int|null
     */
    private $level;

    /**
     * @var int|null
     */
    private $right;

    /**
     * @var \App\Entity\AdminCategory
     */
    private $parent;

    /**
     * @var \App\Entity\AdminCategory
     */
    private $root;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $articles;

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
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->slides = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt(): \DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getLeft(): ?int
    {
        return $this->left;
    }

    /**
     * @param int|null $left
     */
    public function setLeft(?int $left): void
    {
        $this->left = $left;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int|null $level
     */
    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return int|null
     */
    public function getRight(): ?int
    {
        return $this->right;
    }

    /**
     * @param int|null $right
     */
    public function setRight(?int $right): void
    {
        $this->right = $right;
    }

    /**
     * Get parent
     *
     * @return \App\Entity\AdminCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param \App\Entity\AdminCategory $parent
     *
     * @return AdminCategory
     */
    public function setParent(?\App\Entity\AdminCategory $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return AdminCategory
     */
    public function getRoot(): AdminCategory
    {
        return $this->root;
    }

    /**
     * Set root
     *
     * @param \App\Entity\AdminCategory $parent
     *
     * @return AdminCategory
     */
    public function setRoot(AdminCategory $root): void
    {
        $this->root = $root;
    }

    /**
     * Add child
     *
     * @param \App\Entity\AdminCategory $child
     *
     * @return AdminCategory
     */
    public function addChild(\App\Entity\AdminCategory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \App\Entity\AdminCategory $child
     */
    public function removeChild(\App\Entity\AdminCategory $child)
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
     * Add article
     *
     * @param \App\Entity\Article $article
     *
     * @return AdminCategory
     */
    public function addArticle(\App\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \App\Entity\Article $article
     */
    public function removeArticle(\App\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add slide
     *
     * @param \App\Entity\Slider $slide
     *
     * @return AdminCategory
     */
    public function addSlide(\App\Entity\Slider $slide)
    {
        $this->slides[] = $slide;

        return $this;
    }

    /**
     * Remove slide
     *
     * @param \App\Entity\Slider $slide
     */
    public function removeSlide(\App\Entity\Slider $slide)
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

}