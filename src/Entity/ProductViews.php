<?php

namespace App\Entity;


class ProductViews
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int|null
     */
    private $product_id;

    /**
     * @var int|null
     */
    private $views;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    /**
     * @param int $product_id
     * @return ProductViews
     */
    public function setProductId(int $product_id): ProductViews
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getViews(): ?int
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return ProductViews
     */
    public function setViews(int $views): ProductViews
    {
        $this->views = $views;
        return $this;
    }


}