<?php

namespace App\Traits;

trait CommonDbFieldsTrait
{
    /**
     * @var string|null
     */
    private $slug;
    
    /**
     * @var string|null
     */
    private $metadesc;
    
    /**
     * @var string|null
     */
    private $metakey;

    /**
     * @var integer|null
     */
    private $priority;

    /**
     * @var boolean|null
     */
    private $isPublished = false;

    /**
     * @var boolean|null
     */
    private $isCheckedOut = false;

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
    public function getMetadesc(): ?string
    {
        return $this->metadesc;
    }

    /**
     * @param null|string $metadesc
     */
    public function setMetadesc(?string $metadesc): void
    {
        $this->metadesc = $metadesc;
    }

    /**
     * @return null|string
     */
    public function getMetakey(): ?string
    {
        return $this->metakey;
    }

    /**
     * @param null|string $metakey
     */
    public function setMetakey(?string $metakey): void
    {
        $this->metakey = $metakey;
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

    /**
     * @return bool|null
     */
    public function getisCheckedOut(): ?bool
    {
        return $this->isCheckedOut;
    }

    /**
     * @param bool|null $isCheckedOut
     */
    public function setIsCheckedOut(?bool $isCheckedOut): void
    {
        $this->isCheckedOut = $isCheckedOut;
    }

}
