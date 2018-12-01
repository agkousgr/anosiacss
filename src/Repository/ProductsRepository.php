<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function search($keyword)
    {
        return $this->createQueryBuilder('p')
            ->where('p.productName LIKE :value')
            ->orWhere('p.prCode LIKE :value')
            ->setParameter('value', '%' . $keyword . '%')
            ->orderBy('p.productName', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}