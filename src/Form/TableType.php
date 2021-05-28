<?php

namespace App\Form;

use App\Entity\Table;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->categorieChoices = $options['choice_categories'];
        $builder
            ->add('name',TextType::class,[
                'label' => 'Nom de la table'
            ])
            ->add('categorie',EntityType::class,[
                'class' => Categorie::class,
                'placeholder' => "sélectionner la catégorie pour cette table...",
                'choices'=>$this->categorieChoices,
                'choice_label' => 'name'
                ])
            ->add('maxEchs',IntegerType::class,[
                'label' => 'Nombre maxi d\'échantillons sur cette table'
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Enregistrer'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
            'choice_categories' => null
        ]);
    }
}
