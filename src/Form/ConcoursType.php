<?php

namespace App\Form;

use App\Entity\Concours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConcoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('date')
            ->add('lieu')
            ->add('debut_inscription')
            ->add('fin_inscription')
            ->add('cout')
            ->add('tva')
            ->add('resp_name')
            ->add('resp_adress1')
            ->add('resp_adress2')
            ->add('resp_adress3')
            ->add('resp_phone')
            ->add('resp_email')
            ->add('couv_palmares')
            ->add('adress1')
            ->add('adress2')
            ->add('adress3')
            // ->add('concours', EntityType::class, array(
            //     'type' => 'hidden',
            //     'class' => Concours::class
    
            // ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concours::class,
        ]);
    }
}
