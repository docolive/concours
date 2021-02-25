<?php

namespace App\Form;

use App\Entity\Profil;
use App\Entity\Categorie;
use App\Service\ConcoursSession;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProfilType extends AbstractType
{
    private $concoursId;

    public function __construct(ConcoursSession $concoursSession){
        $concours = $concoursSession->recup();
        $this->concoursId = $concours->getId();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('jure',CheckboxType::class,[
            'required'=>false,
            'label' =>"Je souhaite être juré(e)",
            'label_attr' => [
                'style' => 'font-weight:bold'
            ]
        ])
        ->add('choix_degustation', EntityType::class, array(
            'required' => false,
            'multiple' => true,
            'expanded' =>true,
            'placeholder' => '...',
            'label_attr' => [
                'class' => "d-inline",
                //
            ],
            'attr' => [
                //'style' => 'width:100px'
            ],
            'label' => false,
            'class' => Categorie::class,
            'query_builder' => function (CategorieRepository $repo) {
                return $repo->createQueryBuilder('c')
                    ->innerJoin('c.type','t','WITH','t.concours = :concoursId')
                    ->where('t.concours = :concoursId')
                    ->orderBy('c.type', 'ASC')
                    ->setParameter('concoursId',$this->concoursId);
            },
            'choice_label' => 'name',

        ))
            ->add('siret',TextType::class,[
                'required'=>false,
                'label' =>"SIRET (uniquement si vous êtes candidat(e))",
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
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
                'label' =>false,
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress3',TextType::class,[
                'required'=>false,
                'label' =>false,
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress4',TextType::class,[
                'required'=>false,
                'label' =>false,
                'label_attr' => [
                    'style' => 'font-weight:bold'
                ]
            ])
            ->add('adress5',TextType::class,[
                'required'=>false,
                'label' =>false,
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
