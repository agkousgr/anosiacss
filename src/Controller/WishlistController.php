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

class WishlistController extends MainController
{
    public function viewWishlist(WishlistService $wishlistService)
    {
        try {
            $wishlistIds = '';
            if (null !== $this->loggedUser) {
                $wishlistArr = $this->em->getRepository(Wishlist::class)->getWishlistByUser($this->loggedUser);
            } else {
                $wishlistArr = $this->em->getRepository(Wishlist::class)->getWishlistBySession($this->session->getId());
            }

            if ($wishlistArr) {
                foreach ($wishlistArr as $key => $val) {
                    $wishlistIds .= $val->getProductId() . ',';
//                $this->totalCartItems = $this->totalCartItems + 1*$val->getQuantity();
                    $this->totalCartItems = $this->totalCartItems + 1;
                }
                $wishlistIds = substr($wishlistIds, 0, -1);
            }
            $wishlistItems = ($wishlistIds) ?  $wishlistService->getWishlistItems($wishlistIds, $wishlistArr) : '';

            return ($this->render('orders/wishlist.html.twig', [
                'categories' => $this->categories,
                'cartItems' => $this->cartItems,
                'wishlistItems' => $wishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'hideCart' => true
            ]));
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function addToWishlist(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $id = $request->request->get('id');
                $productName = $request->request->get('name');
                $quantity = $request->request->get('quantity');
                $quantity = ($quantity) ?: 1;
                $itemInWishlist = (int)$em->getRepository(Wishlist::class)->checkIfProductExists($session->getId(), $session->get('anosiaUser'), $id);
                dump($itemInWishlist);
                if ($itemInWishlist === 0) {
                    $wishlist = new Wishlist();
                    $wishlist->setProductId($id);
                    $wishlist->setSessionId($session->getId());
                    if (null !== $session->get('anosiaUser')) {
                        $wishlist->setUsername($session->get('anosiaUser'));
                    }
                    dump($wishlist);
                    if (null !== $id) {
                        $em->persist($wishlist);
                        $em->flush();
//                        return $this->redirectToRoute('load_top_wishlist');

                        return $this->json([
                            'success' => true,
                            'exist' => false,
                            'totalWishlistItems' => $em->getRepository(Wishlist::class)->countWishlistItems($session->getId(), $session->get('anosiaUser')),
                            'prName' => $productName,

                        ]);
                    }
                } else {
                    return $this->json([
                        'success' => true,
                        'exist' => true,
                        'prName' => $productName,
                    ]);
                }
//                return $this->redirectToRoute('load_top_wishlist');
                return $this->json(['success' => false]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function deleteWishlistItem(EntityManagerInterface $em, int $id)
    {
        try {
            if (!$id) {
                throw $this->createNotFoundException(
                    'No product found for id ' . $id
                );
            }
            $wishlistItem = $em->getRepository(Wishlist::class)->find($id);
            // Add code for checking that sessionId or Username has access to specific wishlistId
            // Add here
            // End code

            $em->remove($wishlistItem);
            $em->flush();
            return $this->redirectToRoute('wishlist_view');
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
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