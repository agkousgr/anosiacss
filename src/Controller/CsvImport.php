<?php

namespace App\Controller;

use App\Entity\AdminCategory;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\MigrationProducts;
use App\Entity\User;
use League\Csv\Reader;
use League\Csv\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\Kernel;

class CsvImport extends AbstractController
{
    public function importData(EntityManagerInterface $em)
    {
//        dump($kernel);
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/articles.csv', 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->limit(10);

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

    public function importProductCategories(EntityManagerInterface $em)
    {
        try {
            $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/product-categories.csv', 'r');
            $csv->setDelimiter("\t");
//        $csv->setHeaderOffset(0);

            $stmt = (new Statement())
                ->offset(9000)
                ->limit(2000);
            $errors = [];
            $records = $stmt->process($csv);
            foreach ($records as $record) {
                $pr = $em->getRepository(MigrationProducts::class)->findOneBy(['sku' => $record[0]]);
                if ($pr) {
                    $categoryIds = $record[2];
                    if (!$record[3]) {
                        $pr->setName($record[1]);
                        $pr->setCategoryIds($categoryIds);
                        $em->persist($pr);
                        $em->flush();
                        continue;
                    }
                    if (!$record[4]) {
                        $categoryIds .= ',' . $record[3];
                        $pr->setName($record[1]);
                        $pr->setCategoryIds($categoryIds);
                        $em->persist($pr);
                        $em->flush();
                        continue;
                    }
                    if (!$record[5]) {
                        $categoryIds .= ',' . $record[3] . ',' . $record[4];
                        $pr->setName($record[1]);
                        $pr->setCategoryIds($categoryIds);
                        $em->persist($pr);
                        $em->flush();
                        continue;
                    }
                    $categoryIds .= ',' . $record[3] . ',' . $record[4] . ',' . $record[5];
                    $pr->setName($record[1]);
                    $pr->setCategoryIds($categoryIds);
                    $em->persist($pr);
                    $em->flush();
                } else {
                    $errors[] = $record;
                }
            }
            dump($errors);
            return;
        } catch (\Exception $e) {
            if ($record) {
                dump($record);
            }
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function importAlternativeCategories(EntityManagerInterface $em)
    {
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/alternative-categories.csv', 'r');
//        $csv->setDelimiter("\t");
//        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset(10)
            ->limit(650);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
            $altCategory = '';
            $category = $em->getRepository(Category::class)->findOneBy(['s1id' => $record[1]]);
            if (!$category) {
                dump($record[1]);
                die();
            }
            if ($category->getAlternativeCategories()) {
                continue;
            }
            if (!$record[2] && !$record[3] && !$record[4] && !$record[5]) {
                $category->setAlternativeCategories($record[1]);
                $em->persist($category);
                $em->flush();
                continue;
            }
            $altCategory = $record[2];
            if (!$record[3]) {
                $category->setAlternativeCategories($altCategory);
                $em->persist($category);
                $em->flush();
                continue;
            }
            if (!$record[4]) {
                $altCategory .= ',' . $record[3];
                $category->setAlternativeCategories($altCategory);
                $em->persist($category);
                $em->flush();
                continue;
            }
            if (!$record[5]) {
                $altCategory .= ',' . $record[3] . ',' . $record[4];
                $category->setAlternativeCategories($altCategory);
                $em->persist($category);
                $em->flush();
                continue;
            }
            $altCategory .= ',' . $record[3] . ',' . $record[4] . ',' . $record[5];
            $category->setAlternativeCategories($altCategory);
            $em->persist($category);
            $em->flush();
        }
        return;
    }

    public function importProducts(EntityManagerInterface $em)
    {
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/products-import.csv', 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);


        $stmt = (new Statement())
            ->offset(4201)
            ->limit(8600);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
//            if (!$record['sku'])
//                continue;
//            dump($record);
            $pr = new MigrationProducts();
//            $pr->setS1id(intval($record['s1id']));
            $pr->setOldId(intval($record['id']));
            $pr->setName($record['name']);
            $pr->setSku($record['sku']);
            $pr->setSmallDescription($record['smallDescription']);
            $pr->setLargeDescription($record['largeDescription']);
            $pr->setIngredients($record['ingredients']);
            $pr->setInstructions($record['instructions']);
            $pr->setOldSlug($this->initSlug($record['slug']));
            $pr->setSlug($this->initSlug($record['slug']));
            $pr->setImages($record['images']);
//            $pr->setImages($this->initImages($record['images']));
//            $pr->setSeoTitle($record['seoTitle']);
//            $pr->setSeoKeywords($record['seoKeywords']);
            $pr->setManufacturer($record['manufacturer']);
            $pr->setAvailability($record['stock']);
//            dump($pr);
            $em->persist($pr);
            $em->flush();
        }
        return;
    }

    private function initSlug($slug)
    {
        $slugArr = explode('/', $slug);
        return $slugArr[count($slugArr)-2];
    }

    private function initImages($images)
    {
        $image = '';
        $imagesArr = explode('|', $images);
        foreach ($imagesArr as $item) {
            $imageArr = explode('/', $item);
            $image .= end($imageArr) . ',';
        }
        return rtrim($image, ',');
    }

    public function importSeo(EntityManagerInterface $em)
    {
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/SEO.csv', 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset(0)
            ->limit(10000);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
//            dump($record);
            $pr = $em->getRepository(MigrationProducts::class)->findOneBy(['sku' => $record['SKU']]);
            if ($pr) {
//                dump($pr);
                $pr->setSeoTitle(strval($record['seo_title']));
                $pr->setSeoKeywords($record['keywords']);
                $em->persist($pr);
                $em->flush();
            }
        }
        return;
//        $pr = $em->
    }


    public function importOldSlugs(EntityManagerInterface $em)
    {
        $csv = Reader::createFromPath($this->getParameter('kernel.project_dir') . '/public/csv/old_slugs.csv', 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->offset(0)
            ->limit(10000);

        $records = $stmt->process($csv);
        foreach ($records as $record) {
//            dump($record);
            $pr = $em->getRepository(MigrationProducts::class)->findOneBy(['s1id' => $record['id']]);
            if ($pr) {
//                dump($pr);
                $pr->setImages(strval($record['imageUrl']));
                $pr->setOldSlug($record['Slug']);
                $em->persist($pr);
                $em->flush();
            }
        }
        return;
//        $pr = $em->
    }

}