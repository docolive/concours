<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Form\LivraisonType;
use App\Repository\LivraisonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
* @Route("/livraison")
* @IsGranted("ROLE_ADMIN")
*/
class LivraisonController extends AbstractController
{
    /**
     * @Route("/add", name="livraison_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('livraison_index');
        }

        return $this->render('livraison/add.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="livraison_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Livraison $livraison): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('livraison_index');
        }

        return $this->render('livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="livraison_index", methods={"GET"})
     */
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository
            ->findBy(
                array(),
                array()
            )
        ]);
    }

    /**
     * @Route("/{id}", name="livraison_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Livraison $livraison): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            //vérifier que ce dépôt n'est pas utilisé pour un échantillon
            $livraisons = $livraison->getEchantillons();
            if(count($livraisons) > 0){
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer ce dépôt qui est utilisé pour au moins un échantillon'
                );
                return $this->redirectToRoute('livraison_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($livraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livraison_index');
    }
}
