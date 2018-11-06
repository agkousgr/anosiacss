<?php

namespace App\Form\Type;

use App\Entity\Slider;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType,
    FileType,
    IntegerType,
    TextareaType,
    TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
//            ->add('category', EntityType::class, [
//                'class' => 'App\Entity\Category',
//                'choice_label' => 'name',
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('c')
//                        ->orderBy('c.id');
//                },
//                'placeholder' => 'Επιλέξτε κατηγορία',
//                'required' => false
//            ])
            ->add('priority', IntegerType::class)
            ->add('url', TextType::class, [
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
            'data_class' => Slider::class,
        ));
    }
}