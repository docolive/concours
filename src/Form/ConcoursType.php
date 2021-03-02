<?php

namespace App\Form;

use App\Entity\Concours;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ConcoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('date', DateType::class, array(
                'required' => true,
                'attr' => [
                    'style' => 'width:160px'
                ],
                'label_attr' => [
                    'class' => "mt-4",
                    
                ],
                'label' => 'Date du Concours',
                // renders it as a single text box
                'widget' => 'single_text',
                //'block_name' => 'datepicker',
            ))
            ->add('lieu')
            // ->add('adress1')
            // ->add('adress2')
            // ->add('adress3')
            ->add('debut_inscription', DateType::class, array(
                'required' => true,
                'attr' => [
                    'style' => 'width:160px'
                ],
                'label_attr' => [
                    'class' => "mt-4",
                    
                ],
                'label' => 'Date de début des inscriptions',
                // renders it as a single text box
                'widget' => 'single_text',
                //'block_name' => 'datepicker',
            ))
            ->add('fin_inscription', DateType::class, array(
                'required' => true,
                'attr' => [
                    'style' => 'width:160px'
                ],
                'label_attr' => [
                    'class' => "mt-4",
                    
                ],
                'label' => 'Date de clôture des inscriptions',
                // renders it as a single text box
                'widget' => 'single_text',
                //'block_name' => 'datepicker',
            ))
            ->add('cout',MoneyType::class,array(
                'label' => "Coût HT par échantillon"
            ))
            ->add('tva',IntegerType::class,array(
                'label' => "TVA"
            ))
            ->add('resp_name',TextType::class,array(
                'label' => 'Nom Prénom du Responsable'
            ))
            ->add('resp_adress1',TextType::class,array(
                'label' => 'Adresse du Responsable'
            ))
            ->add('resp_adress2',TextType::class,array(
                'label' => 'Adresse suite'))
            ->add('resp_adress3',TextType::class,array(
                'label' => 'Adresse suite'))
            ->add('resp_phone',TextType::class,array(
                'label' => 'Tél du Responsable'))
            ->add('resp_email',EmailType::class,array(
                'label' => 'Email du Responsable'))
            ->add('couv_palmares',TextType::class,array(
                'required' => false,
                'label' => 'Couverture du Palmarès'))
           
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Concours::class,
        ]);
    }
}
