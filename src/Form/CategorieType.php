<?php

namespace App\Form;

use App\Entity\Type;
use App\Entity\Categorie;
use App\Service\ConcoursSession;
use App\Repository\TypeRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    private $concoursId;

    public function __construct(ConcoursSession $concoursSession){
        $concours = $concoursSession->recup();
        $this->concoursId = $concours->getId();
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ordre')
            ->add('name')
            ->add('type', EntityType::class, array(
                'required' => true,
                'placeholder' => '...',
                'label_attr' => [
                    'class' => "mt-4",
                    //
                ],
                'attr' => [
                    //'style' => 'width:100px'
                ],
                'label' => 'Type',
                'class' => Type::class,
                'query_builder' => function (TypeRepository $repo) {
                    return $repo->createQueryBuilder('t')
                        ->where('t.concours = :concoursId')
                        ->orderBy('t.ordre', 'ASC')
                        ->setParameter('concoursId',$this->concoursId);
                },
                'choice_label' => 'nom',
    
            ))
            ->add('participe',ChoiceType::class,[
                'choices'=>[
                    'catégorie ouverte' => true,
                    'catégorie fermée' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
