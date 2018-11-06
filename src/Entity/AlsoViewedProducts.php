<?php


namespace App\Entity;


class AlsoViewedProducts
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $alsoViewedId;

    /**
     * @var int
     */
    private $views = 0;

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
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     * @return AlsoViewedProducts
     */
    public function setProductId(int $productId): AlsoViewedProducts
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlsoViewedId(): int
    {
        return $this->alsoViewedId;
    }

    /**
     * @param int $alsoViewedId
     * @return AlsoViewedProducts
     */
    public function setAlsoViewedId(int $alsoViewedId): AlsoViewedProducts
    {
        $this->alsoViewedId = $alsoViewedId;
        return $this;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return AlsoViewedProducts
     */
    public function setViews(int $views): AlsoViewedProducts
    {
        $this->views = $views;
        return $this;
    }

}