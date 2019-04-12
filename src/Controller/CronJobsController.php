<?php

namespace App\Controller;

use App\Entity\{AvailabilityTypes, Product};
use App\Service\{
    CronJobsService, ProductService, SoftoneLogin
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class CronJobsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $authId;

    public function __construct(EntityManagerInterface $em, SoftoneLogin $softoneLogin)
    {
        $this->em = $em;
        $this->authId = $softoneLogin->login();
    }

    public function synchronizeProducts(ProductService $productService, LoggerInterface $logger)
    {
        $prevName = '';
        $duplicates = [];
        $sub = new \DateInterval("PT5M"); // Interval of 5 mins
        $curDate = new \DateTime('now', new \DateTimeZone('Europe/Athens'));
        $curDate->sub($sub);
        $formattedDate = $curDate->format('Y-m-d\TH:i:s');
        try {
            $s1products = $productService->getItems('null', 'null', 100, 'NameAsc', -1, 'null', 'null', 'null', -1, 'null', 0, $formattedDate);
            if ($s1products) {
                foreach ($s1products as $s1product) {
//                    if ($prevName === strval($s1product['name'])) {
//                        $duplicates[] = strval($s1product['name']);
//                        continue;
//                    } else {
//                        $prevName = strval($s1product['name']);
//                    }
                    $webVisible = (strval($s1product['webVisible']) === 'true') ? true : false;
                    $active = (strval($s1product['active']) === 'true') ? true : false;
                    $pr = $this->em->getRepository(Product::class)->find(intval($s1product['id']));
                    $webPrice = round(floatval($s1product['retailPrice']) * (100 - floatval($s1product['webDiscount'])) / 100, 2);
                    if ($pr) {
                        $pr->setSlug(strval($s1product['slug']));
                        $pr->setPrCode(strval($s1product['prCode']));
                        $pr->setBarcode(strval($s1product['mainBarcode']));
                        $pr->setProductName(strval($s1product['name']));
                        $pr->setImage(strval($s1product['imageUrl']));
                        $pr->setRetailPrice(floatval($s1product['retailPrice']));
                        $pr->setDiscount(floatval($s1product['webDiscount']));
                        $pr->setWebPrice(floatval($webPrice));
                        $pr->setWebVisible($webVisible);
                        $pr->setActive($active);
                        $this->em->flush();
                    } else {
                        if (strval($s1product['id']) !== '') {
                            $pr = new Product();
                            $pr->setId(intval($s1product['id']));
                            $pr->setSlug(strval($s1product['slug']));
                            $pr->setPrCode(strval($s1product['prCode']));
                            $pr->setBarcode(strval($s1product['mainBarcode']));
                            $pr->setProductName(strval($s1product['name']));
                            $pr->setImage(strval($s1product['imageUrl']));
                            $pr->setRetailPrice(strval($s1product['retailPrice']));
                            $pr->setDiscount(strval($s1product['webDiscount']));
                            $pr->setWebPrice(floatval($webPrice));
                            $pr->setWebVisible($webVisible);
                            $pr->setActive($active);
                            $this->em->persist($pr);
                            $this->em->flush();
                        }
                    }
                }
            }

            return new Response('Products synchronization completed');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeCategories(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            // Todo: Check how to remove deleted category from S1
            $service->synchronizeCategories($this->authId);

            return new Response('Categories synchronization completed');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function syncCategoryTopSellers(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->syncCategoryTopSellers();

            return new Response('Category top sellers synchronization completed');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeAvailabilityTypes(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $avTypes = $service->getAvailabilityTypes(-1, $this->authId);
            if ($avTypes) {
                foreach ($avTypes as $avType) {
                    $at = new AvailabilityTypes();
                    $at->setS1id(intval($avType->ID));
                    $at->setName($avType->Name);
                    $at->setFromDays($avType->FromDays);
                    $at->setToDays($avType->ToDays);
                    $this->em->persist($at);
                }
                $this->em->flush();
            }
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeParameters(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->getParams();
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeCountries(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->getCountries();
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
