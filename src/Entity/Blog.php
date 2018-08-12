<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 20/5/2018
 * Time: 2:39 μμ
 */

namespace App\Entity;

use App\Traits\{
    BasicDbFieldsTrait, BlameableTrait, TimestampableTrait
};
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class Blog
{
    use BasicDbFieldsTrait, BlameableTrait, TimestampableTrait;

    /**
     * @var string|null
     */
    protected $slug;

    /**
     * @var string|null
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
     * @var BlogCategory|null
     */
    private $category;

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
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     */
    public function setImage(?string $image): void
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
     * @return BlogCategory|null
     */
    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    /**
     * @param BlogCategory|null $category
     */
    public function setCategory(?BlogCategory $category): void
    {
        $this->category = $category;
    }

}