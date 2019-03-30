<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function search(string $keyword, string $transliterated): array
    {
        $arr = explode(' ', $keyword);
        if (count($arr) > 1) {
            $sql = "
                SELECT *, MATCH (product_name) AGAINST ('" . $keyword . "') AS relevance
                FROM product
                WHERE MATCH (product_name) AGAINST ('" . $keyword . "')
                OR product_name LIKE '%" . $keyword . "%'
                OR product_name LIKE '%" . $transliterated . "%'
                OR pr_code LIKE '%" . $keyword . "%'
                OR pr_code LIKE '%" . $transliterated . "%'
                OR barcode LIKE '%" . $keyword . "%'
                OR barcode LIKE '%" . $transliterated . "%'
                ORDER BY relevance DESC
                LIMIT 10
            ";
        } else {
            $sql = "
                SELECT *
                FROM product
                WHERE product_name LIKE '%" . $keyword . "%'
                OR product_name LIKE '%" . $transliterated . "%'
                OR pr_code LIKE '%" . $keyword . "%'
                OR pr_code LIKE '%" . $transliterated . "%'
                OR barcode LIKE '%" . $keyword . "%'
                OR barcode LIKE '%" . $transliterated . "%'
                LIMIT 10
            ";
        }
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getLatestOffers()
    {
        return $this->createQueryBuilder('p')
            ->where('p.latestOffer IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}