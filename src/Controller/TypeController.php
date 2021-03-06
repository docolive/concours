<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Entity\Concours;
use App\Service\ConcoursSession;
use App\Repository\TypeRepository;
use App\Repository\ConcoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/type")
 * @IsGranted("ROLE_ADMIN")
 */
class TypeController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    
    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository): Response
    {
        return $this->render('type/index.html.twig', [
            'types' => $typeRepository
            ->findBy(
                array(),
                array('ordre' => 'ASC')
            )
        ]);
    }

    /**
     * @Route("/add", name="type_add", methods={"GET","POST"})
     */
    public function add(Request $request, ConcoursRepository $concoursRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $concoursId = $concours->getId();
            $concours = $concoursRepository->find($concoursId);
            $type->setConcours($concours);
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $entityManager->persist($type);
            $entityManager->flush();

            return $this->redirectToRoute('type_index');
        }

        return $this->render('type/add.html.twig', [
            'type' => $type,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Type $type): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_index');
        }

        return $this->render('type/add.html.twig', [
            'type' => $type,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Type $type): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            //vérifier que ce type n'est pas utilisé dans une catégorie
            $categories = $type->getCategories();
            if(count($categories) > 0){
                $this->addFlash(
                    'warning',
                    'Impossible de supprimer ce type qui est utilisé dans au moins une catégorie'
                );
                return $this->redirectToRoute('type_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($type);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_index');
    }
}
