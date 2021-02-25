<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Form\PaiementType;
use App\Repository\PaiementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
* @Route("/paiement")
* @IsGranted("ROLE_ADMIN")
*/
class PaiementController extends AbstractController
{
    /**
     * @Route("/add", name="paiement_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $paiement = new Paiement();
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($paiement);
            $entityManager->flush();

            return $this->redirectToRoute('paiement_index');
        }

        return $this->render('paiement/add.html.twig', [
            'paiement' => $paiement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="paiement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Paiement $paiement): Response
    {
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($paiement);
            $entityManager->flush();

            return $this->redirectToRoute('paiement_index');
        }

        return $this->render('paiement/edit.html.twig', [
            'paiement' => $paiement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="paiement_index", methods={"GET"})
     */
    public function index(PaiementRepository $paiementRepository): Response
    {
        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiementRepository
            ->findBy(
                array(),
                array()
            )
        ]);
    }

    /**
     * @Route("/{id}", name="paiement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Paiement $paiement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paiement->getId(), $request->request->get('_token'))) {
            //vérifier que ce dépôt n'est pas utilisé pour un échantillon
            $paiements = $paiement->getEchantillons();
            if(count($paiements) > 0){
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer ce mode de paiement qui est utilisé pour au moins un échantillon'
                );
                return $this->redirectToRoute('paiement_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($paiement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('paiement_index');
    }
}
