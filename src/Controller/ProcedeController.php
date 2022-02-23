<?php

namespace App\Controller;

use App\Entity\Procede;
use App\Form\ProcedeType;
use App\Entity\Concours;
use App\Service\ConcoursSession;
use App\Repository\ProcedeRepository;
use App\Repository\ConcoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/procede")
 * @IsGranted("ROLE_ADMIN")
 */
class ProcedeController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    
    /**
     * @Route("/", name="procede_index", methods={"GET"})
     */

    public function index(ProcedeRepository $procedeRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        return $this->render('procede/index.html.twig', [
            'procedes' => $procedeRepository->findFromConcours($concours)
        ]);
    }

    /**
     * @Route("/add", name="procede_add", methods={"GET","POST"})
     */
    public function add(Request $request, ConcoursRepository $concoursRepository): Response
    {
        $procede = new procede();
        $form = $this->createForm(ProcedeType::class, $procede);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($procede);
            $entityManager->flush();

            return $this->redirectToRoute('procede_index');
        }

        return $this->render('procede/add.html.twig', [
            'procede' => $procede,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="procede_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Procede $Procede): Response
    {
        $form = $this->createForm(ProcedeType::class, $Procede);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('procede_index');
        }

        return $this->render('procede/add.html.twig', [
            'procede' => $Procede,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="procede_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Procede $procede): Response
    {
        if ($this->isCsrfTokenValid('delete'.$procede->getId(), $request->request->get('_token'))) {
            //vérifier que ce  n'est pas utilisé dans une catégorie
            $categories = $procede->getCategorie();
            //dd($categories);
            if(!null === $categories){
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer cette sous-catégorie qui est utilisée dans une catégorie'
                );
                return $this->redirectToRoute('procede_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($procede);
            $entityManager->flush();
        }

        return $this->redirectToRoute('procede_index');
    }
}
