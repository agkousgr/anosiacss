<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nextPage', HiddenType::class, array(
                'data' => 3
            ))
            ->add('address', TextType::class)
            ->add('zip', TextType::class)
            ->add('city', TextType::class)
            ->add('district', TextType::class)
            ->add('phone01', TextType::class)
            ->add('series', ChoiceType::class, array(
                'choices' => array(
                    'Απόδειξη' => 7021,
                    'Τιμολόγιο' => 7023,
                ),
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('afm', TextType::class, array(
                'data' => 'No Invoice'
            ))
            ->add('irs', TextType::class, array(
                'data' => 'No Invoice'
            ))
            ->add('shipAddress', TextType::class, [
                'required' => false
            ])
            ->add('shipZip', TextType::class, [
                'required' => false
            ])
            ->add('shipCity', TextType::class, [
                'required' => false
            ])
            ->add('shipDistrict', TextType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}