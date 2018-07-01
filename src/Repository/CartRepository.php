<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 20/5/2018
 * Time: 9:09 μμ
 */

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CartRepository
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @param $sessionId
     * @return cart[]
     */
    public function getCartBySession($sessionId) :array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.session_id=:sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery();

        return $qb->execute();
    }
}