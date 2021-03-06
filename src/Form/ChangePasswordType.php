<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'label' => 'mot de passe actuel ',
                    'required' => true,
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'les deux mots de passe doivent être identiques.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options' => ['label' => 'Nouveau mot de passe :'],
                    'second_options' => ['label' => 'Confirmation :'],
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Valider les modifications',]);
    }

//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
////        $builder
////            ->add('oldPassword', PasswordType::class, ['label'=>'mot de passe actuel ',
////                'required'=>true
////            ])
//        ->add('password', RepeatedType::class, [
//        'type' => PasswordType::class,
//        'invalid_message' => 'les deux mots de passe doivent être identiques.',
//        'options' => ['attr' => ['class' => 'password-field']],
//        'required' => true,
//        'first_options'  => ['label' => 'Nouveau mot de passe :'],
//        'second_options' => ['label' => 'Confirmation :'],
//    ])
//        ->add('save', SubmitType::class, ['label'=>'Valider les modifications',])
//    ;
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ResetPassword::class,
            ]
        );
    }
}
