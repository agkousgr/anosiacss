<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function search($keyword)
    {
//        $result = $yourRepository->createQueryBuilder('p')
//            ->addSelect("MATCH_AGAINST (p.fieldName1, p.fieldName2, p.fieldName3, :searchterm 'IN NATURAL MODE') as score")
//            ->add('where', 'MATCH_AGAINST(p.fieldName1, p.fieldName2, p.fieldName3, :searchterm) > 0.8')
//            ->setParameter('searchterm', "Test word")
//            ->orderBy('score', 'desc')
//            ->getQuery()
//            ->getResult();

//        return $this->createQueryBuilder('p')
//            ->addSelect("MATCH_AGAINST (p.productName, p.prCode, p.barcode, :searchterm 'IN NATURAL MODE') as score")
//            ->add('where', 'MATCH_AGAINST(p.productName, p.prCode, p.barcode, :searchterm) > 0.8')
//            ->setParameter('searchterm', $keyword)
//            ->orderBy('score', 'desc')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();

        return $this->createQueryBuilder('p')
            ->where('p.productName LIKE :value')
            ->orWhere('p.prCode LIKE :value')
            ->orWhere('p.barcode LIKE :value')
            ->setParameter('value', '%' . $keyword . '%')
            ->orderBy('p.productName', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}