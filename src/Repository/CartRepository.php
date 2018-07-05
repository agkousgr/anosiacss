<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 20/5/2018
 * Time: 9:09 μμ
 */

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
            ->andWhere('c.session_id=:sessionId')
            ->setParameter('sessionId', $sessionId)
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

//    /**
//     * @param $sessionId
//     * @param null $username
//     * @param $prId
//     *
//     * @return Cart
//     */
//    public function getItem($sessionId, $username = null, $prId)
//    {
//        $qb = $this->getEntityManager()->createQueryBuilder()
//            ->select('c')
//            ->from('App:Cart', 'c');
//        if (null !== $username) {
//            $qb->andWhere('c.username=:username')
//                ->andWhere('c.product_id=:prId')
//                ->setParameters(array('username' => $username, 'prId' => $prId));
//        } else {
//            $qb->andWhere('c.session_id=:sessionId')
//                ->andWhere('c.product_id=:prId')
//                ->setParameters(array('sessionId' => $sessionId, 'prId' => $prId));
//        }
//        return $qb->getQuery()->getResult();
//    }
}