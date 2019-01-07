<?php


namespace App\Form\Type;

use App\Entity\Products;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType,
    FileType,
    TextareaType,
    TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LatestOffersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('latestOffer',DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\Category',
                'choice_label' => 'slug',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id');
                },
                'placeholder' => 'Επιλέξτε κατηγορία',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, array(
                'required' => false,
                'label' => 'Δημοσίευση'
            ))
            ->add('image', FileType::class, [
                'required' => false,
                'data_class' => null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Products::class,
        ));
    }
}