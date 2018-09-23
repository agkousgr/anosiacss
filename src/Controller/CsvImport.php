<?php

namespace App\Controller;

use App\Entity\Article;
use League\Csv\Reader;
use League\Csv\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\Kernel;

class CsvImport extends MainController
{
    public function importData(EntityManagerInterface $em)
    {
//        dump($kernel);
        $article = new Article();
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/articles.csv', 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->limit(500);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
            $article->setName($record['name']);
            $article->setDescription($record['description']);
            $imageArr = explode('/', $record['image']);
            $article->setImage(end($imageArr));
            $em->persist($article);
        }
        $em->flush();
        return;

    }
}