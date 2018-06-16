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
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }


}