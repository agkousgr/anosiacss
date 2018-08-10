<?php

namespace App\Traits;

trait CommonDbFieldsTrait
{
    /**
     * @var string
     */
    private $slug;
    
    /**
     * @var string
     */
    private $metadesc;
    
    /**
     * @var string
     */
    private $metakey;

    /**
     * @var integer
     */
    private $ordering;

    /**
     * @var boolean
     */
    private $isPublished = false;

    /**
     * @var boolean
     */
    private $isCheckedOut = false;

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set metadesc
     *
     * @param string $metadesc
     *
     * @return self
     */
    public function setMetadesc($metadesc = null)
    {
        $this->metadesc = $metadesc;

        return $this;
    }

    /**
     * Get metadesc
     *
     * @return string
     */
    public function getMetadesc()
    {
        return $this->metadesc;
    }

    /**
     * Set metakey
     *
     * @param string $metakey
     *
     * @return self
     */
    public function setMetakey($metakey = null)
    {
        $this->metakey = $metakey;

        return $this;
    }

    /**
     * Get metakey
     *
     * @return string
     */
    public function getMetakey()
    {
        return $this->metakey;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
     *
     * @return self
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return self
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isCheckedOut
     *
     * @param boolean $isCheckedOut
     *
     * @return self
     */
    public function setIsCheckedOut($isCheckedOut)
    {
        $this->isCheckedOut = $isCheckedOut;

        return $this;
    }

    /**
     * Get isCheckedOut
     *
     * @return boolean
     */
    public function getIsCheckedOut()
    {
        return $this->isCheckedOut;
    }
}
