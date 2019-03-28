<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CartRepository
 */
class CartRepository extends EntityRepository
{

    /**
     * @param $sessionId
     * @return cart[]
     */
    public function getCartBySession($sessionId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.product', 'p')
            ->andWhere('c.session_id=:sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * @param $username
     * @return array
     */
    public function getCartByUser($username): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.product', 'p')
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
            $qb->select('COUNT(c.product)')
                ->where('c.username = :username')
                ->andWhere('IDENTITY(c.product) = :prId')
                ->setParameters(array('username' => $username, 'prId' => $prId));
        } else {
            $qb->select('COUNT(c.product)')
                ->where('c.session_id = :sessionId')
                ->andWhere('IDENTITY(c.product) = :prId')
                ->setParameters(array('sessionId' => $sessionId, 'prId' => $prId));
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $sessionId
     * @param null $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCartItems($sessionId, $username = null)
    {
        $qb = $this->createQueryBuilder('c');
        if (null !== $username) {
            $qb->select('COUNT(c.product)')
                ->where('c.username = :username')
                ->setParameter('username', $username);
        } else {
            $qb->select('COUNT(c.product)')
                ->where('c.session_id = :sessionId')
                ->setParameter('sessionId', $sessionId);
        }
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $sessionId
     * @param null $username
     * @param $prId
     *
     * @return Cart
     */
    public function getItem($sessionId, $username = null, $prId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('c');
        if (null !== $username) {
            $qb->andWhere('c.username = :username')
                ->andWhere('IDENTITY(c.product) = :prId')
                ->setParameters(array('username' => $username, 'prId' => $prId));
        } else {
            $qb->andWhere('c.session_id = :sessionId')
                ->andWhere('IDENTITY(c.product) = :prId')
                ->setParameters(array('sessionId' => $sessionId, 'prId' => $prId));
        }
        return $qb->getQuery()->getResult();
    }
}