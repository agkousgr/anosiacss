<?php

namespace App\Form\EventListener;

use App\Entity\{Article, Slider, HomePageOurCorner};
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class UploadListener
{
    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(FileUploader $uploader, TokenStorageInterface $tokenStorage)
    {
        $this->uploader = $uploader;
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if (empty($this->tokenStorage->getToken()) || null === $this->tokenStorage->getToken()) {
            $entity = $args->getEntity();
            switch (true) {
                case $entity instanceof Article:
                    $filePath = 'articles_image_directory';
                    break;

                case $entity instanceof Slider:
                    $filePath = 'slider_image_directory';
                    break;

                case $entity instanceof HomePageOurCorner:
                    $filePath = 'our_corner_image_directory';
                    break;

                default:
                    return;
                    break;

            }
            if ($fileName = $entity->getImage()) {
                $entity->setImage(new File($this->uploader->getTargetDirectory()[$filePath] . '/' . $fileName));
            }
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for existing entities
        dump($entity);
        switch (true) {
            case $entity instanceof Article:
                $file = $entity->getImage();
                $filePath = 'articles_image_directory';
                break;

            case $entity instanceof Slider:
                $file = $entity->getImage();
                $filePath = 'slider_image_directory';
                break;

            case $entity instanceof HomePageOurCorner:
                $file = $entity->getImage();
                $filePath = 'our_corner_image_directory';
                break;

            default:
                return;
                break;

        }
        dump($file);
        // only upload new files
        if ($file instanceof UploadedFile) {
            die();
            $fileName = $this->uploader->upload($file, $filePath);
            $entity->setImage($fileName);
        } elseif ($file instanceof File) {
            dump('zong');
            die();
            // prevents the full file path being saved on updates
            // as the path is set on the postLoad listener
            $entity->setImage($file->getFilename());
        }
    }
}