<?php


namespace App\Entity;


class TempImages
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $s1id;

    /**
     * @var string|null
     */
    private $images;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return TempImages
     */
    public function setS1id(int $s1id): TempImages
    {
        $this->s1id = $s1id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImages(): ?string
    {
        return $this->images;
    }

    /**
     * @param null|string $images
     * @return TempImages
     */
    public function setImages(?string $images): TempImages
    {
        $this->images = $images;
        return $this;
    }


}