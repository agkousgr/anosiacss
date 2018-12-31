<?php


namespace App\Entity;

use App\Traits\{
    BasicDbFieldsTrait, BlameableTrait, TimestampableTrait
};

class HomePageOurCorner
{
    use BasicDbFieldsTrait, BlameableTrait, TimestampableTrait;

    /**
     * @var string|null
     */
    private $image;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var bool|null
     */
    private $isPublished;

    /**
     * @var Category
     */
    private $category;

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return HomePageOurCorner
     */
    public function setImage(?string $image): HomePageOurCorner
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return HomePageOurCorner
     */
    public function setDescription(?string $description): HomePageOurCorner
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getisPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool|null $isPublished
     * @return HomePageOurCorner
     */
    public function setIsPublished(?bool $isPublished): HomePageOurCorner
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return HomePageOurCorner
     */
    public function setCategory(Category $category): HomePageOurCorner
    {
        $this->category = $category;
        return $this;
    }
}