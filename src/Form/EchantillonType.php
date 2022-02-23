<?php

namespace App\Form;

use App\Entity\Procede;
use App\Entity\Categorie;
use App\Entity\Echantillon;
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
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EchantillonType extends AbstractType
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
        
        ->add('lot')
        ->add('volume')
            ->add('description')
            ->add('variety', TextType::class,[
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom de la variété des olives de table',
                ],
                'label' => false
            ])
       
            
        ;

        $formModifier = function (FormInterface $form, Categorie $categorie = null) {
            if(null === $categorie) {
                return;
                }else{
                    $this->categorie = $categorie;
                    $pro = $this->er->findProcedes($categorie->getId());
                    //dd($pro);
                    if(!empty($pro)){

                        $form->add('procede', EntityType::class, [
                            'class' => Procede::class,
                            'label' => 'Sous-catégorie',
                            'required' => false,
                            'placeholder' => '...',
                            'query_builder' => function (ProcedeRepository $prepo) {
                                return $prepo->createQueryBuilder('p')
                                ->where('p.categorie = :categorie')
                                ->setParameter('categorie',$this->categorie);
                            },
                            'choice_label' => 'name',
                            ]);
                    }
                }  
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getCategorie());
            }
        );

        $builder->get('categorie')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $categorie = $event->getForm()->getData();
                //dd($categorie);
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $categorie);
            }
        );
    }

   

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Echantillon::class,
            'validation_groups' => ['add']
        ]);
    }
}
