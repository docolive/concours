<?php

namespace App\Form;

use App\Entity\Table;
use App\Entity\Procede;
use App\Entity\Categorie;
use App\Repository\ProcedeRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;



class TableType extends AbstractType
{

    private $procedeRepository;

    public function __construct(ProcedeRepository $procedeRepository){
        $this->procedeRepository = $procedeRepository;
    }
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
            
        ;

        $formModifier = function (FormInterface $form, Categorie $categorie = null) {
            if(null === $categorie) {
                return;
                }else{
                    $this->categorie = $categorie;
                    $pro = $this->procedeRepository->findProcedes($categorie->getId());
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
            'data_class' => Table::class,
            'choice_categories' => null
        ]);
    }
}
