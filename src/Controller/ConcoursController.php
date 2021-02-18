<?php

namespace App\Controller;

use App\Entity\Concours;
use App\Form\ConcoursType;
use App\Repository\ConcoursRepository;
use App\Service\ConcoursSession;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/concours")
 * @IsGranted("ROLE_ADMIN")
 */
class ConcoursController extends AbstractController
{
    /**
     * @Route("/", name="concours_index", methods={"GET"})
     */
    public function index(ConcoursRepository $concoursRepository): Response
    {
        return $this->render('concours/index.html.twig', [
            'concourses' => $concoursRepository
            ->findBy(
                array(),
                array('id' => 'DESC')
            )
        ]);
    }

    /**
     * @Route("/choix", name="concours_choix", methods={"GET"})
     */
    public function choix(ConcoursRepository $concoursRepository): Response
    {
        return $this->render('concours/choix.html.twig', [
            'concourses' => $concoursRepository
            ->findBy(
                array(),
                array('id' => 'DESC')
            )
        ]);
    }

    /**
     * @Route("/{id}/session", name="concours_session", methods={"GET","POST"})
     */
    public function session(Request $request, Concours $concours, ConcoursSession $concoursSession): Response
    {
         $concoursSession->stocke($concours);
         return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/add", name="concours_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $concours = new Concours();
        $form = $this->createForm(ConcoursType::class, $concours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($concours);
            $entityManager->flush();

            return $this->redirectToRoute('concours_index');
        }

        return $this->render('concours/add.html.twig', [
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="concours_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Concours $concours): Response
    {
        $form = $this->createForm(ConcoursType::class, $concours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('concours_index');
        }

        return $this->render('concours/add.html.twig', [
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="concours_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Concours $concours): Response
    {
        if ($this->isCsrfTokenValid('delete'.$concours->getId(), $request->request->get('_token'))) {
            //dd($concours);
            //vérifier que ce concours n'est pas utilisé dans un type
            $types = $concours->getTypes();
            $i = 0;
            foreach($types as $type){
                $i++;
            }
            if($i > 0){
                //dd($types);
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer ce concours qui est utilisé dans au moins un type'
                );
                return $this->redirectToRoute('concours_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($concours);
            $entityManager->flush();
        }

        return $this->redirectToRoute('concours_index');
    }
}
