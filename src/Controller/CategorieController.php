<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Service\ConcoursSession;
use App\Repository\CategorieRepository;
use App\Repository\ConcoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Rpequest;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/categorie")
 * @IsGranted("ROLE_ADMIN")
 */
class CategorieController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    
    /**
     * @Route("/", name="categorie_index", methods={"GET"})
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository
            ->findBy(
                array(),
                array('ordre' => 'ASC')
            )
        ]);
    }

    /**
     * @Route("/add", name="categorie_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/add.html.twig', [
            'categorie' => $categorie,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categorie $categorie): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/add.html.twig', [
            'categorie' => $categorie,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            //  TODO : vérifier que ce type n'est pas utilisé pour un échantillon
            // $categories = $type->getCategories();
            // if(count($categories) > 0){
            //     $this->addFlash(
            //         'warning',
            //         'Impossible de supprimer ce type qui est utilisé dans au moins une catégorie'
            //     );
            //     return $this->redirectToRoute('type_index');
            // }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index');
    }
}
