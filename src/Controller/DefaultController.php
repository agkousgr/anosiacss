<?php

namespace App\Controller;

class DefaultController extends MainController
{
    public function index()
    {
        // Load categories from XML format to array
//        $ctgEntity = $prCategories->InitializeCategories($categoriesXML, $session->get("authID"));

//        $latest = $pr->getItems(-1, $session->get("authID"));
        return $this->render('layout.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'totalCartItems' => $this->totalCartItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
//            'latest' => $latest
        ]);
    }

    public function notFound()
    {
        return $this->render('404/404.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
//            'latest' => $latest
        ]);
    }
}