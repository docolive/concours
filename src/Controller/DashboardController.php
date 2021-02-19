<?php

namespace App\Controller;

use App\Service\ConcoursSession;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DashboardController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }

    /**
     * @Route("/ajax", name="cherche_vol_min_ajax", methods={"POST"})
     */
    public function chercheVolMinAjax(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorieId = $request->request->get('categorieId');

        $categorie = $categorieRepository->find($categorieId);
        
        return new Response($categorie->getType()->getVolMinLot());
    }

    /**
     * @Route("/ajax/nbre", name="cherche_nbre_ech_ajax", methods={"POST"})
     */
    public function chercheNbreEchAjax(Request $request, CategorieRepository $categorieRepository, EchantillonRepository $echantillonRepository): Response
    {
        $categorieId = $request->request->get('categorieId');

        $categorie = $categorieRepository->find($categorieId);
        $type = $categorie->getType();
        //dd($type);

        $user = $this->getUser();

        $echs = $echantillonRepository->findEchMemeType($user,$type);

        return new Response(count($echs) + 1);
    }

    /**
     * @Route("/ajax/max", name="cherche_max_type_ajax", methods={"POST"})
     */
    public function chercheMaxTypeAjax(Request $request, CategorieRepository $categorieRepository, EchantillonRepository $echantillonRepository): Response
    {
        $categorieId = $request->request->get('categorieId');

        $categorie = $categorieRepository->find($categorieId);
        $type = $categorie->getType();
        //dd($type);

        return new Response($type->getNbreMaxEch());
    }


    /**
     * @Route("/", name="dashboard")
     * @IsGranted("ROLE_CANDIDAT")
     */
    public function index(EchantillonRepository $echantillonRepository): Response
    {
        //choix du concours
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        //récupération de l'user
        $user = $this->getUser();
        //dd($user);
        if(!$user->isVerified()){
            dd('halte');
        }
        //récupération du profil
        $profil = $user->getProfil();
        //dd($profil);

        //recupération des échantillons
        $echantillons = $echantillonRepository->findAllEchUser($user,$concours);
            

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'profil' => $profil,
            'concours' => $concours,
            'echantillons' => $echantillons
        ]
        );
    }
}
