<?php

namespace App\Entity;

use App\Traits\{
    BasicDbFieldsTrait, BlameableTrait, TimestampableTrait
};
use http\Env\Url;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class Slider
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
     * @var string|null
     */
    private $url;

    /**
     * @var int|null
     */
    private $priority;

    /**
     * @var bool|null
     */
    private $isPublished;

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
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
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
     */
    public function setIsPublished(?bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }
}