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
        return $this->render('echantillon/index.html.twig', [
            'concours' => $concours,
            'echantillons' => $echantillonRepository
            ->findBy(
                array(),
                array('categorie' => 'ASC')
            )
        ]);
    }

    /**
     * @Route("/add", name="echantillon_add", methods={"GET","POST"})
     */
    public function add(Request $request): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $echantillon = new Echantillon();
        $form = $this->createForm(EchantillonType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           //code public
            $echantillon->setPublicRef('APOAI');
            $user = $this->getUser();
            $echantillon->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($echantillon);
            $entityManager->flush();

            return $this->redirectToRoute('echantillon_index');
        }

        return $this->render('echantillon/add.html.twig', [
            'categorie' => $echantillon,
            'concours' => $concours,
            'form' => $form->createView(),
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
        $form = $this->createForm(EchantillonType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('echantillon_index');
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

        return $this->redirectToRoute('echantillon_index');
    }
}
