<?php

namespace App\Form\Type;


use App\Entity\AdminCategory;
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

class AdminCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', TextType::class)
            ->add('parent', EntityType::class, [
                'class' => 'App\Entity\AdminCategory',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id');
                },
                'placeholder' => 'Επιλέξτε γονική κατηγορία',
                'required' => false
            ])
            ->add('metakey', TextareaType::class, [
                'required' => false
            ])
            ->add('metadesc', TextareaType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('priority', IntegerType::class)
            ->add('isPublished', CheckboxType::class, array(
                'required' => false,
                'label' => 'Δημοσίευση'
            ))
//            ->add('image', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AdminCategory::class,
        ));

    }
}