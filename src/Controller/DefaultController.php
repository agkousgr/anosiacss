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
            'featured' => $this->featured,
            'popular' => $this->popular,
            'loggedUser' => $this->loggedUser
//            'latest' => $latest
        ]);
    }
}