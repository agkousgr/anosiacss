<?php

namespace App\Form\Type;

use App\Entity\WebUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGeneralInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
            ));
//            ->add('special_offers', CheckboxType::class, array(
//                'required' => false,
//            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}