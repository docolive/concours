<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Entity\Echantillon;
use App\Service\PDFService;
use App\Service\ConcoursSession;
use Symfony\Component\Mime\Email;
use App\Repository\LivraisonRepository;
use App\Repository\EchantillonRepository;
use App\Repository\PaiementRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/validation")
 * @IsGranted("ROLE_CANDIDAT")
 */
class ValidationController extends AbstractController
{
    private $session;
    private $pdf;
    public function __construct(ConcoursSession $concoursSession, PDFService $pdf){
        $this->session = $concoursSession;
        $this->pdf = $pdf;
    }

    /**
     * @Route("/email",name="bulletin_mail")
     */
    public function sendEmail(MailerInterface $mailer, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $concours = $this->session->recup();
        $admins = $userRepository->findByRoleAdmin();
        $cc = "c.aitikhlef@ctolivier.org";

        // foreach($admins as $a){
        //     $cc[]= $a->getEmail();
        // }
        //dd($cc);
        $message = "Bonjour ".$user->getProfil()->getPrenom().' '.$user->getProfil()->getNom().",";
        $message .= "<br><br>Nous avons bien enregistré votre inscription au Concours ".$concours->getName()." et vous en remercions.";
        $message .= "<br><br>Votre participation sera effective dès que nous aurons reçu vos échantillons dans le centre de dépôt que vous avez choisi, ainsi que vos frais de participation.";
        if($user->getProfil()->getJure()){
            $message .= "<br><br>Nous avons enregistré votre souhait d'être juré(e) dans ce Concours et vous en remercions.
            <br>Nous vous contacterons pour la suite à donner.";
        }else{
            $message .= "<br><br>Vous avez la possibilité d'être éventuellement juré(e) dans ce Concours.<br>Il suffit de <a href=\"https://concours.ctolivier.org\">vous connecter</a> et de cliquer sur \"m'inscrire comme juré(e)\" .";
        }
        $message .= "<br><br>Vous pouvez contacter les organisateurs au 04 42 23 84 80 ou bien par mail : c.aitikhlef@ctolivier.org .";
        $message .= "<br><br>Vous trouverez ci-joint votre bulletin d'inscription.";
        $message .= "<br><br><br>Cordialement,";
        $message .= "<br><br><br>Les organisateurs du Concours";

        $email = (new Email())
            ->from('contact@franceolive.com')
            ->to($user->getEmail())
            ->cc($cc)
            //->addCc($cc[2])
            //->addCc($cc[3])
            ->bcc('docolivefr@gmail.com')
            ->replyTo('contact@franceolive.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Bulletin de participation au concours ')
            //->text('Sending emails is fun again!')
            ->html($message)
            ->attachFromPath($_SERVER['DOCUMENT_ROOT'].'bulletin_inscription_concours'.$user->getId().'.pdf','bulletin_participation_concours')
;

        $mailer->send($email);
        $this->addFlash(
            'success',
            'Un email contenant votre bulletin de participation vous a été envoyé.'
        );
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/phase1", name="validation_phase1")
     */
    public function phase1(): Response
    {
        $concours = $this->session->recup();
        $user = $this->getUser();

        //checking coordonnées
        $profil = $user->getProfil();
        if(is_null($profil)){

            $this->addFlash(
                'warning',
                'Merci de compléter vos coordonnées'
            );
            return $this->redirectToRoute('profil_add');
        }
        if(!is_null($profil)){
            if(is_null($profil->getRaisonSociale())){
                if(is_null($profil->getNom())){
                    $this->addFlash(
                        'warning',
                        'Merci de compléter vos coordonnées'
                    );
                    return $this->redirectToRoute('profil_edit',['id'=> $profil->getId()]);
                }
            }
        }

        return $this->redirectToRoute('validation_phase2');
    }

    /**
     * @Route("/phase2", name="validation_phase2")
     */
    public function phase2(EchantillonRepository $echantillonRepository,LivraisonRepository $livraisonRepository, PaiementRepository $paiementRepository, Request $request): Response
    {
        $concours = $this->session->recup();
        $user = $this->getUser();
        $echantillons = $echantillonRepository->findAllEchUser($user,$concours);
        $HT = round(count($echantillons) * $concours->getCout(),2);
        $tauxTVA = round($concours->getTVA(),2);
        $TVA = round($HT * $tauxTVA / 100,2);
        $TTC = $HT + $TVA;

        $livraisons = $livraisonRepository->findAll();
        $options = array();
        foreach($livraisons as $l){
            $coordonnee = $l->getName();
            $coordonnee .= ' / '.$l->getAdress1();
            $coordonnee .= '  '.$l->getAdress2();
            $coordonnee .= '  '.$l->getAdress3();
            $coordonnee .= ' / '.$l->getPhone();
            $coordonnee .= ' / '.$l->getHoraire();
            $options[$coordonnee] = $l->getId();
        }
        $paiements = $paiementRepository->findAll();
        $optionsPaie = array();
        foreach($paiements as $p){
            $mode= $p->getName();
            $optionsPaie[$mode] = $p->getId();
        }
        $form = $this->createFormBuilder()
            ->add('depot', ChoiceType::class, array(
                'attr' => [
                    //'class' => 'mt-4'
                ],

                'label'=>false,
                'label_attr' => [
                ],
                'multiple' => false,
                'required' => true,
                'expanded' => true,
                'choices'  => $options,

            ))
            ->add('paie', ChoiceType::class, array(
                'label' => false,
                
                'multiple' => false,
                'required' => true,
                'expanded' => true,
                'choices'  => $optionsPaie,

            ))
            ->add('Enregistrer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            $livraison = $livraisonRepository->find($data['depot']);
            foreach($echantillons as $e){
                $e->setLivraison($livraison);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($e);
                $entityManager->flush();
            }

            $paiement = $paiementRepository->find($data['paie']);
            foreach($echantillons as $e){
                $e->setPaiement($paiement);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($e);
                $entityManager->flush();
            }

        $this->pdf->bulletin($concours,$user,$echantillons);
            //return $this->redirectToRoute('dashboard');
            
            return $this->redirectToRoute('bulletin_mail');
        }

        return $this->render('validation/phase2.html.twig',array(
            'form' => $form->createView(),
            'echantillons' => $echantillons,
            'concours' => $concours,
            'HT' => $HT,
            'tauxTVA' => $tauxTVA,
            'TVA' => $TVA,
            'TTC' => $TTC,
        ));
    }

   
}
