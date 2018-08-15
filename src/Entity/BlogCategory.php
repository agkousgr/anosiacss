<?php

namespace App\Entity;

use App\Traits\{BasicDbFieldsTrait, TimestampableTrait, BlameableTrait, CommonDbFieldsTrait};

class BlogCategory
{
    use BasicDbFieldsTrait, TimestampableTrait, BlameableTrait, CommonDbFieldsTrait;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var \App\Entity\BlogCategory
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
     * @var \App\Entity\BlogCategory|null
     */
    private $root;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $blogs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->blogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return BlogCategory
     */
    public function setDeletedAt(\DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }


    /**
     * Add child
     *
     * @param \App\Entity\BlogCategory $child
     *
     * @return BlogCategory
     */
    public function addChild(\App\Entity\BlogCategory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \App\Entity\BlogCategory $child
     */
    public function removeChild(\App\Entity\BlogCategory $child)
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
     * Add blog
     *
     * @param \App\Entity\Blog $blog
     *
     * @return Blog
     */
    public function addBlog(\App\Entity\Blog $blog)
    {
        $this->blog[] = $blog;

        return $this;
    }

    /**
     * Remove blog
     *
     * @param \App\Entity\Blog $blog
     */
    public function removeBlog(\App\Entity\Blog $blog)
    {
        $this->blog->removeElement($blog);
    }

    /**
     * Get blogs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlogs()
    {
        return $this->blog;
    }

    /**
     * Set parent
     *
     * @param \App\Entity\BlogCategory $parent
     *
     * @return BlogCategory
     */
    public function setParent(\App\Entity\BlogCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return  \App\Entity\BlogCategory
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
     * @return BlogCategory|null
     */
    public function getRoot(): ?BlogCategory
    {
        return $this->root;
    }

    /**
     * Set root
     *
     * @param \App\Entity\BlogCategory $parent
     *
     * @return BlogCategory
     */
    public function setRoot(?BlogCategory $root): void
    {
        $this->root = $root;
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

}
