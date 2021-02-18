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
            ->add('roles', ChoiceType::class, array(
				'label_attr'=>array('class'=>'sr-only'),
				'attr'  =>  array('class' => 'form-control',
						'style' => 'margin:5px 0;'),
				'choices' =>
				array
				(
						//'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
						'Administrateur du concours' => 'ROLE_ADMIN',
						'Candidat' => 'ROLE_CANDIDAT',
						
                        
				) ,
				'multiple' => true,
				'required' => true,
		)
				)
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
