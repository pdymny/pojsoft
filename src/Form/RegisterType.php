<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name_company', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Nazwa firmy'
                ))
            )
            ->add('street', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Ulica i numer lokalu'
                ))
            )
            ->add('code_post', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Kod pocztowy'
                ))
            )
            ->add('city', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Miejscowość'
                ))
            )
            ->add('nip', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'NIP'
                ))
            )
            ->add('regon', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Regon'
                ))
            )

            ->add('firstname', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Imię'
                ))
            )
            ->add('name', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Nazwisko'
                ))
            )
            ->add('email', EmailType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'E-mail'
                ))
            )
            ->add('phone', TextType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control mt-3',
                    'placeholder' => 'Telefon'
                ))
            )
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => false, 'attr' => array('class' => "form-control mt-3", 'placeholder' => 'Hasło')),
                'second_options' => array('label' => false, 'attr' => array('class' => "form-control mt-3", 'placeholder' => 'Powtórz hasło')),
            ))
            ->add('akcept', CheckboxType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'mt-3',
                ))
            )
            ->add('submit', SubmitType::class, array(
                'label' => 'Załóż konto',
                'attr' => array(
                    'class' => 'btn btn-lg btn-success btn-block',
                    'style' => 'margin-top:15px;margin-bottom:10px;'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}