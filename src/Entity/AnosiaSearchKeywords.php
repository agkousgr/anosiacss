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
     * @var Category
     */
    private $category;

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
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return AnosiaSearchKeywords
     */
    public function setCategory(Category $category): AnosiaSearchKeywords
    {
        $this->category = $category;
        return $this;
    }


}