<?php

namespace App\Form\Type;


use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType,
    FileType,
    IntegerType,
    TextareaType,
    TextType};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', TextType::class)
            ->add('metakey', TextareaType::class, [
                'required' => false
            ])
            ->add('metadesc', TextareaType::class, [
                'required' => false
            ])
            ->add('summary', TextareaType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('name');
                },
                'placeholder' => 'Επιλέξτε κατηγορία'
            ])
            ->add('isPublished', CheckboxType::class, [
                'required' => false,
                'label' => 'Δημοσίευση'
            ])
            ->add('image', FileType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AdminCategory::class,
        ));

    }
}