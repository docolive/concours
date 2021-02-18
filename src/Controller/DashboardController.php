<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @IsGranted("ROLE_CANDIDAT")
     */
    public function index(): Response
    {
        //récupération de l'user
        $user = $this->getUser();
        //dd($user);
        //récupération du profil
        $profil = $user->getProfil();
        //dd($profil);

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'profil' => $profil
        ]
        );
    }
}
