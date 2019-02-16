<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class SliderRepository extends EntityRepository
{
    public function getSlider()
    {
        return $this->createQueryBuilder('s')
            ->where('s.adminCategory IS NULL')
            ->andWhere('s.category IS NULL')
            ->andWhere('s.isPublished = ?1')
            ->setParameter(1, true)
            ->getQuery()
            ->getResult();
    }
}