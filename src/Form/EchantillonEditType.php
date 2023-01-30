<?php

namespace App\Form;

use App\Entity\Procede;
use App\Entity\Categorie;
use App\Entity\Echantillon;
use App\Entity\Medaille;
use App\Service\ConcoursSession;
use Doctrine\DBAL\Types\BooleanType;
use App\Repository\ProcedeRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EchantillonEditType extends AbstractType
{
    private $categorie;
    private $concoursId;
    private $er;
    private $repo;
    public function __construct(ConcoursSession $concoursSession, ProcedeRepository $er,CategorieRepository $repo){
        $concours = $concoursSession->recup();
        $this->concoursId = $concours->getId();
        $this->er = $er;
        $this->repo = $repo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('recu', CheckboxType::class,[
                'required' => false,
                'label' => 'Reçu'
            ])
            ->add('paye', CheckboxType::class,[
                'required' => false,   
                
                'label' => 'Réglé'
            ])
            ->add('public_ref', TextType::class,[
                'label' => 'code public',
                'attr'=>[
                    'readonly' => true,   
                ]
            ])
            ->add('code', TextType::class,[
                'label' => 'code anonyme',
                'attr'=>[
                    'readonly' => true,   
                ]
            ])
            ->add('description', TextType::class,[
                'label' => 'description',
                'required' => false,
                'attr'=>[
                    //'readonly' => true,   
                ]
            ])
            ->add('medaille',EntityType::class,[
                'class'=>Medaille::class,
                'placeholder' => "...",   
                'required' => false,
                'label' => 'Médaille',
                
            ])
            
            ->add('enregistrer',SubmitType::class,[
                'label'=>'Enregistrer'
            ])
            
        ;

        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Echantillon::class,

            
        ]);
    }
}
