<?php

namespace App\Controller;

use App\Entity\Echantillon;
use App\Service\PDFService;
use App\Service\ConcoursSession;
use Symfony\Component\Mime\Email;
use App\Repository\EchantillonRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/pdf", name="validation_pdf")
     */
    public function pdf(EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        $user = $this->getUser();
        $echantillons = $echantillonRepository->findAllEchUser($user,$concours);
        return new Response($this->pdf->bulletin($concours,$user,$echantillons));
    }

    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $user = $this->getUser();
        $email = (new Email())
            ->from('contact@franceolive.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Bulletin de participation au concours ')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
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
    public function phase2(EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        $user = $this->getUser();
        $echantillons = $echantillonRepository->findAllEchUser($user,$concours);
        $HT = count($echantillons) * $concours->getCout();
        $tauxTVA = $concours->getTVA();
        $TVA = $HT * $tauxTVA / 100;
        $TTC = count($echantillons) * 20;

        return $this->render('validation/phase2.html.twig',array(
            'echantillons' => $echantillons,
            'concours' => $concours,
            'HT' => $HT,
            'tauxTVA' => $tauxTVA,
            'TVA' => $TVA,
            'TTC' => $TTC,
        ));

    }
}
