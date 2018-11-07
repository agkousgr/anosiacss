<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class AnosiaSearchKeywordsRepository extends EntityRepository
{
//$result = $em->getRepository("Orders")->createQueryBuilder('o')
//->where('o.OrderEmail = :email')
//->andWhere('o.Product LIKE :product')
//->setParameter('email', 'some@mail.com')
//->setParameter('product', 'My Products%')
//->getQuery()
//->getResult();
    public function getAnosiaSearchResult($keyword)
    {

        return $this->createQueryBuilder('ask')
            ->where('ask.keyword LIKE :value')
            ->setParameter('value', '%' . $keyword . '%')
            ->orderBy('ask.keyword', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}