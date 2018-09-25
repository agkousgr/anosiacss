<?php

namespace App\Form\EventListener;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Slider;
use App\Service\FileUploader;

class UploadListener
{
    /**
     * @var FileUploader
     */
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
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
        $entity = $args->getEntity();
        switch(true) {
            case $entity instanceof Article:
                $filePath = 'articles_image_directory';
                break;

            case $entity instanceof Slider:
                $filePath = 'slider_image_directory';
                break;

            default:
                return;
                break;

        }

        if ($fileName = $entity->getImage()) {
            $entity->setImage(new File($this->uploader->getTargetDirectory()[$filePath].'/'.$fileName));
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for existing entities
        switch(true) {
            case $entity instanceof Article:
                $file = $entity->getImage();
                $filePath = 'articles_image_directory';
                break;

            case $entity instanceof Slider:
                $file = $entity->getImage();
                $filePath = 'slider_image_directory';
                break;

            default:
                return;
                break;

        }

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file, $filePath);
            $entity->setImage($fileName);
        } elseif ($file instanceof File) {
            // prevents the full file path being saved on updates
            // as the path is set on the postLoad listener
            $entity->setImage($file->getFilename());
        }
    }
}