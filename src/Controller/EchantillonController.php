<?php
namespace App\Controller;

use App\Entity\Echantillon;
use App\Form\EchantillonType;
use App\Service\ConcoursSession;
use App\Repository\ConcoursRepository;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\EchantillonEditType;
use App\Repository\ProcedeRepository;

/**
 * @Route("/echantillon")
 * @IsGranted("ROLE_CANDIDAT")
 */
class EchantillonController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }

    /**
     * @Route("/", name="echantillon_index", methods={"GET"})
     */
    public function index(EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $echantillons = $echantillonRepository->findEchConcours($concours);
        //dd($concours);
        return $this->render('echantillon/index.html.twig', [
            'concours' => $concours,
            'echantillons' => $echantillons
        ]);
    }

    public function codePublic($categorie): string
    {
            $repo = $this->getDoctrine()->getRepository(Echantillon::class);
            $random = rand(1,100);
            $shuffled = str_shuffle('ABCDEFGHIJKLM');
            $codePublic = $categorie.'-'.$random.'-'.substr($shuffled,0,3);
            $existe = $repo->findBy(
                ['public_ref' => $codePublic]
            );
            if(empty($existe)){
                return $codePublic;
            }else{
                $this->codePublic($categorie);
            }
    }

    /**
     * @Route("/add", name="echantillon_add", methods={"GET","POST"})
     */
    public function add(Request $request,ProcedeRepository $procedeRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $user = $this->getUser();
        if($user->getProfil()){
            $SIRET = $user->getProfil()->getSiret();
            if(strlen($SIRET) == 0){
                $this->addFlash(
                    'error',
                    'Votre numéro de SIRET est obligatoire pour participer au Concours.'
                );
                return $this->redirectToRoute('profil_edit');
            }
        }else{
            $this->addFlash(
                'error',
                'Votre numéro de SIRET est obligatoire pour participer au Concours.'
            );
            return $this->redirectToRoute('profil_add');
        }
        $echantillon = new Echantillon();
        $echantillon->setUser($user);
        $form = $this->createForm(EchantillonType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            
           //code public
            $categorie = $echantillon->getCategorie()->getId();
            $codePublic = $this->codePublic($categorie);
            //dd($codePublic);
            $echantillon->setPublicRef($codePublic);
            $user = $this->getUser();
            $echantillon->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($echantillon);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('echantillon/add.html.twig', [
            //'echantillon' => $echantillon,
            'concours' => $concours,
            'form' => $form->createView(),
            //'add' => true
        ]);
    }

    /**
     * @Route("/{id}/edit", name="echantillon_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Echantillon $echantillon): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $form = $this->createForm(EchantillonEditType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');

        }

        return $this->render('echantillon/add.html.twig', [
            'categorie' => $echantillon,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="echantillon_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Echantillon $echantillon): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echantillon->getId(), $request->request->get('_token'))) {
        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($echantillon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard');

    }
}
