<?php

namespace App\Repository;

use App\Entity\Wishlist;
use Doctrine\ORM\EntityRepository;

/**
 * Class WishlistRepository
 */
class WishlistRepository extends EntityRepository
{

    /**
     * @param $sessionId
     * @return wishlist[]
     */
    public function getWishlistBySession($sessionId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.session_id=:sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * @param $username
     * @return array
     */
    public function getWishlistByUser($username): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.username=:username')
            ->setParameter('username', $username)
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * @param $sessionId
     * @param null $username
     * @param $prId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkIfProductExists($sessionId, $username = null, $prId)
    {
        $qb = $this->createQueryBuilder('c');
        if (null !== $username) {
            $qb->select('COUNT(c.product_id)')
                ->where('c.username=:username')
                ->andWhere('c.product_id=:prId')
                ->setParameters(array('username' => $username, 'prId' => $prId));
        } else {
            $qb->where('c.session_id=:sessionId')
                ->andWhere('c.product_id=:prId')
                ->setParameters(array('sessionId' => $sessionId, 'prId' => $prId))
                ->select('COUNT(c.product_id)');
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $sessionId
     * @param null $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countWishlistItems($sessionId, $username = null)
    {
        $qb = $this->createQueryBuilder('c');
        if (null !== $username) {
            $qb->select('COUNT(c.product_id)')
                ->where('c.username=:username')
                ->setParameter('username', $username);
        } else {
            $qb->where('c.session_id=:sessionId')
                ->setParameter('sessionId', $sessionId)
                ->select('COUNT(c.product_id)');
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $sessionId
     * @param null $username
     * @param $prId
     *
     * @return Wishlist
     */
    public function getItem($sessionId, $username = null, $prId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c');
        if (null !== $username) {
            $qb->andWhere('c.username=:username')
                ->andWhere('c.product_id=:prId')
                ->setParameters(array('username' => $username, 'prId' => $prId));
        } else {
            $qb->andWhere('c.session_id=:sessionId')
                ->andWhere('c.product_id=:prId')
                ->setParameters(array('sessionId' => $sessionId, 'prId' => $prId));
        }
        return $qb->getQuery()->getResult();
    }
}