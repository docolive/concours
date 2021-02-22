<?php

namespace App\Controller;

use App\Repository\EchantillonRepository;
use App\Service\ConcoursSession;
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
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
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
