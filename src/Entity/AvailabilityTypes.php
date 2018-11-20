<?php


namespace App\Entity;


class AvailabilityTypes
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
    private $name;

    /**
     * @var string|null
     */
    private $fromDays;

    /**
     * @var string|null
     */
    private $toDays;

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
     * @return AvailabilityTypes
     */
    public function setS1id(int $s1id): AvailabilityTypes
    {
        $this->s1id = $s1id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return AvailabilityTypes
     */
    public function setName(?string $name): AvailabilityTypes
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFromDays(): ?string
    {
        return $this->fromDays;
    }

    /**
     * @param null|string $fromDays
     * @return AvailabilityTypes
     */
    public function setFromDays(?string $fromDays): AvailabilityTypes
    {
        $this->fromDays = $fromDays;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getToDays(): ?string
    {
        return $this->toDays;
    }

    /**
     * @param null|string $toDays
     * @return AvailabilityTypes
     */
    public function setToDays(?string $toDays): AvailabilityTypes
    {
        $this->toDays = $toDays;
        return $this;
    }


}