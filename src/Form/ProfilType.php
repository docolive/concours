<?php

namespace App\Form;

use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('email', TextType::class, [
            //     'mapped' => false,
            //         'attr' => [
            //             'readonly'=>true
            //         ],
            //     ])
            ->add('raison_sociale',TextType::class,[
                'required'=>false,
                'label' =>"Raison sociale",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('nom',TextType::class,[
                'required'=>false,
                'label' =>"Nom",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('prenom',TextType::class,[
                'required'=>false,
                'label' =>"Prénom",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress1',TextType::class,[
                'required'=>false,
                'label' =>"Adresse",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress2',TextType::class,[
                'required'=>false,
                'label' =>"Adresse suite",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress3',TextType::class,[
                'required'=>false,
                'label' =>"Adresse suite",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress4',TextType::class,[
                'required'=>false,
                'label' =>"Adresse suite",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress5',TextType::class,[
                'required'=>false,
                'label' =>"Adresse suite",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('phone',TextType::class,[
                'required'=>false,
                'label' =>"Téléphone",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
