<?php

namespace App\Controller;

use App\Service\ConcoursSession;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DashboardController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }

    /**
     * @Route("/ajax/checkProcede", name="checkProcede", methods={"POST"})
     */
    public function checkProcedeAjax(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorieId = $request->request->get('categorieId');
        $categorie = $categorieRepository->find($categorieId);
        $response = array();
        foreach($categorie->getProcedes() as $p){
            $response[$p->getId()] = $p->getName();
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/ajax/checkOT", name="checkOT", methods={"POST"})
     */
    public function checkOTAjax(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorieId = $request->request->get('categorieId');
        $categorie = $categorieRepository->find($categorieId);
        if(!is_null($categorie)){
            return new Response($categorie->getType()->getOtable());
        }else{
            return new Response('false');
        }
    }

    /**
     * @Route("/ajax", name="cherche_vol_min_ajax", methods={"POST"})
     */
    public function chercheVolMinAjax(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorieId = $request->request->get('categorieId');

        $categorie = $categorieRepository->find($categorieId);
        if(!is_null($categorie)){
            return new Response($categorie->getType()->getVolMinLot());
        }else{
            return new Response(10000000);
        }
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
        if($type->getOtable()){

        }else{
            $echs = $echantillonRepository->findEchMemeType($user,$type);
        }

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
        $mail = $user->getEmail();
        if(!$user->isVerified()){
            $this->addFlash(
                'warning',
                "Votre adresse mail n'a pas encore été vérifiée. Merci de lire le mail que nous avons envoyé à l'adresse $mail lors de votre inscription."
            );
            $this->addFlash(
                'warning',
                'Le sujet du mail est "Merci de confirmer votre inscription au Concours"'
            );
            return $this->render('dashboard/missingmail.html.twig',[
                'mail' => $mail
            ]);
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
