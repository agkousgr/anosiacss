<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 20/5/2018
 * Time: 7:42 μμ
 */

namespace App\Entity;

use App\Traits\{
    TimestampableTrait
};

class Cart
{
    use TimestampableTrait;

    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string
     */
    private $session_id;

    /**
     * @var \App\Entity\Product|null
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->session_id;
    }

    /**
     * @param string $session_id
     */
    public function setSessionId(string $session_id): void
    {
        $this->session_id = $session_id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return Cart
     */
    public function setProduct(?Product $product): Cart
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}