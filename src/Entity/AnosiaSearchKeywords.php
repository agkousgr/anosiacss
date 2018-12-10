<?php


namespace App\Entity;


class AnosiaSearchKeywords
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $keyword;

    /**
     * @var int
     */
    private $category_id;

    /**
     * @var string
     */
    private $slug;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @return AnosiaSearchKeywords
     */
    public function setKeyword(string $keyword): AnosiaSearchKeywords
    {
        $this->keyword = $keyword;
        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     * @return AnosiaSearchKeywords
     */
    public function setCategoryId(int $category_id): AnosiaSearchKeywords
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return AnosiaSearchKeywords
     */
    public function setSlug(string $slug): AnosiaSearchKeywords
    {
        $this->slug = $slug;
        return $this;
    }

}