<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('Roles', ChoiceType::class, [
                'mapped'=>false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices'  => [
                  'Candidat ou Juré' => 'ROLE_CANDIDAT',
                  'Administrateur' => 'ROLE_ADMIN',
                ],
            ])
            ->add('password')
            ->add('isVerified')
            ;
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
