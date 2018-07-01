<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 23/5/2018
 * Time: 1:14 πμ
 */

namespace App\Controller;


use App\Entity\Wishlist;
use App\Service\WishlistService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WishlistController extends AbstractController
{
    public function addToWishlist(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                dump($request);
                $prId = $request->query->getInt('id');
                dump($prId);
                $wishlist = new Wishlist;
                $wishlist->setProductId(29076);
                $wishlist->setSessionId($session->getId());
                $date = new \DateTime("now");
                $wishlist->setCreatedAt($date);
                $wishlist->setUpdatedAt($date);
                if (null !== $session->get('username')) {
                    $wishlist->setUsername($session->get('username'));
                }
                dump($wishlist);
                if (null !== $prId) {
                    $em->persist($wishlist);
                    $em->flush();

                    return ($this->render('partials/top_wishlist.html.twig'));
                }
                return $this->json(['success' => false]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function loadTopWishlist(EntityManagerInterface $em, WishlistService $wishlist, SessionInterface $session)
    {
//        $wishlist = new WishlistService();
        $wishlistIds = '';
        if (null === $session->get('username')) {
            $wishlistArr = $em->getRepository(Wishlist::class)->getWishlistBySession($session->getId());
            if ($wishlistArr) {
                foreach ($wishlistArr as $key => $val) {
                    $wishlistIds .= $val->getProductId() . ',';
                }
                $wishlistIds = substr($wishlistIds, 0 , -1);
            }
        }

        $wishlistItems = ($wishlistIds) ? $wishlist->getWishlistItems($wishlistIds) : null;
//        dump($wishlistItems);
        return ($this->render('partials/top_wishlist.html.twig', [
            'wishlistItems' => $wishlistItems
        ]));
    }
}