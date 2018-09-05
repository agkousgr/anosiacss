<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/9/2018
 * Time: 7:40 μμ
 */

namespace App\Service;


class SkroutzService
{
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function initilizeSkroutzXml()
    {
        $products = $this->productService->getItems('null', ' ');
        return $products;
    }

}