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
        $products = $this->productService->getItems('null', 'null', '10000', 'null', '1' );
        $initializedProducts = $this->initializeProducts($products);
        $xmlOutput = $this->createXml($initializedProducts);
        return $xmlOutput;
    }

    private function initializeProducts($products)
    {
        return $products;
    }

    private function createXml($products)
    {
        $counter = 0;
//        $xml_output = '';
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>    
        <volleyplus>
            <created_at>' . date('Y-m-d H:i') . '</created_at>
                <products>';



        $xml_output .= '</products>
</volleyplus>';
        return $xml_output;
    }

}