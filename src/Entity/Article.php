<?php

namespace App\Entity;

use App\Traits\{
    BasicDbFieldsTrait, BlameableTrait, TimestampableTrait, CommonDbFieldsTrait
};

class Article
{
    use BasicDbFieldsTrait, BlameableTrait, TimestampableTrait, CommonDbFieldsTrait;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Παρακαλώ επιλέξτε μια εικόνα")
     * @Assert\File(mimeTypes={ "image/gif, image/png, image/jpeg" })
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
     * @var AdminCategory
     */
    private $category;

    /**
     * @return null|string
     */
    public function getImage(): ?string
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
     * @return BlogCategory
     */
    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    /**
     * Set BlogCategory
     *
     * @param \App\Entity\BlogCategory $facilityOwner
     *
     * @return BlogCategory
     */
    public function setCategory(?BlogCategory $category): void
    {
        $this->category = $category;
    }

}