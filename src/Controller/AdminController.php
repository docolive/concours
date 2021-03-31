<?php

namespace App\Controller;

use App\Entity\Echantillon;
use App\Service\ConcoursSession;
use App\Repository\UserRepository;
use App\Repository\ProcedeRepository;
use App\Repository\EchantillonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/admin")
*/
class AdminController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    
    /**
     * @Route("/candidat/echantillons/{userId}", name="admin_candidat_echantillons")
     */
    function EchantillonsCandidat(EchantillonRepository $echantillonRepository, UserRepository $userRepository, $userId): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        $user = $userRepository->find($userId);

        $echantillons = $echantillonRepository->findAllEchUser($user, $concours);
        // dd($echantillons);
        return $this->render('admin/candidat/echantillons.html.twig', [
            'echantillons' => $echantillons,
            'concours' => $concours,
            'user' => $user
        ]);
    }

}
