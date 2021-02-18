<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Echantillon;
use App\Service\ConcoursSession;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchantillonType extends AbstractType
{
    private $concoursId;

    public function __construct(ConcoursSession $concoursSession){
        $concours = $concoursSession->recup();
        $this->concoursId = $concours->getId();
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('lot')
            ->add('volume')
           
            ->add('categorie', EntityType::class, array(
                'required' => true,
                'placeholder' => '...',
                'label_attr' => [
                    'class' => "mt-4",
                    //
                ],
                'attr' => [
                    //'style' => 'width:100px'
                ],
                'label' => 'Catégorie',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Echantillon::class,
        ]);
    }
}
