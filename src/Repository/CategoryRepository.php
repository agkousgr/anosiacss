<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function getTopLevelCategoriesAndDirectChildren(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('c, children')
            ->from('App:Category', 'c')
            ->join('c.children', 'children')
            ->where('c.s1Level = :level')
            ->orderBy('c.priority')
            ->setParameter('level', 0);

        return $qb->getQuery()->getResult();
    }
}
