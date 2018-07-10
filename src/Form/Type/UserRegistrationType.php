<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/6/2018
 * Time: 2:29 μμ
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, PasswordType, TextType, EmailType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('username', EmailType::class, array(
                'required' => true,
                'label' => 'Όνομα Χρήστη (email)'
            ))
//            ->add('address', TextType::class)
//            ->add('zip', TextType::class)
//            ->add('city', TextType::class)
//            ->add('district', TextType::class)
//            ->add('phone01', TextType::class)
            ->add('password', PasswordType::class)
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('special_offers', CheckboxType::class, array(
                'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setRequired('username');
    }
}