<?php

namespace App\Controller;

use App\Entity\AdminCategory;
use App\Entity\Article;
use App\Entity\User;
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
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/articles.csv', 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->limit(500);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
        $article = new Article();
            $article->setName($record['name']);
            $article->setDescription($record['description']);
            $category = $em->getRepository(AdminCategory::class)->find(1);
            dump($category);
            $article->setCategory($category);
            $article->setSlug($record['slug']);
            $article->setSummary($record['summary']);
            $article->setCreatedAt(new \DateTime($record['created_at']));
            $article->setUpdatedAt(new \DateTime($record['created_at']));
            $user = $em->getRepository(User::class)->find(1);
            dump($user);
            $article->setCreatedBy($user);
            $article->setUpdatedBy($user);
//            $imageArr = explode('/', $record['image']);
//            $article->setImage(end($imageArr));
            $article->setImage($record['image']);
            $isPublished = ($record['is_published'] === 'publish') ? true : false;
            $article->setIsPublished($isPublished);
            dump($article);
            $em->persist($article);
        $em->flush();
        }
        return;

    }
}