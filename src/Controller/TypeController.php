<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Entity\Concours;
use App\Repository\CategorieRepository;
use App\Service\ConcoursSession;
use App\Repository\TypeRepository;
use App\Repository\ConcoursRepository;
use App\Repository\ProcedeRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/admin/type")
 * @IsGranted("ROLE_ADMIN")
 */
class TypeController extends AbstractController
{
    private $session;
    private $entityManager;
    public function __construct(ConcoursSession $concoursSession, EntityManagerInterface $entityManager){
        $this->session = $concoursSession;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/", name="type_index", methods={"GET"})
     */
    public function index(TypeRepository $typeRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        return $this->render('type/index.html.twig', [
            'types' => $typeRepository
            ->findFromConcours($concours)
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
            // dd($type);
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
     * @Route("/duplicate/{concours_a_dupliquer_id}", name="type_duplicate")
     */
    public function duplicate(ConcoursRepository $concoursRepository, TypeRepository $typeRepository,$concours_a_dupliquer_id,CategorieRepository $categorieRepository, ProcedeRepository $procedeRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        
        //récupération du concours à dupliquer
        $concours_a_dupliquer = $concoursRepository->find($concours_a_dupliquer_id);
        

        //récupération des types du concours à dupliquer
        $types = $typeRepository->findFromConcours($concours_a_dupliquer_id);

        foreach($types as $t){
            //dd($t);
            $conn = $this->entityManager->getConnection();
            $nom = str_replace("'","\'",$t->getNom());
            $nom = str_replace("/","\/",$t->getNom());
            $otable = 0;
            if($t->getOtable() == true){
                $otable = 1;
            }
            $varieteOt = 'null';
            if($t->getVarieteOT() != null){
                $varieteOt = $t->getVarieteOT();
            }
            $sql = '
                INSERT INTO type (ordre,nom,vol_min_lot,nbre_max_ech,concours_id,unite,otable,varieteot) VALUES ('.$t->getOrdre().','.'"'.$nom.'"'.','.$t->getVolMinLot().','.$t->getNbreMaxEch().','.$concours->getId().','.'"'.$t->getUnite().'"'.','.$otable.','.$varieteOt.')';
                //dd($sql);
            $resultSet = $conn->executeStatement($sql);
        }
            //catégories
            //récupération des catégories du concours à dupliquer
            $categories = $categorieRepository->findFromConcours($concours_a_dupliquer_id);
        foreach($categories as $c){
            //dd($c);
            $conn = $this->entityManager->getConnection();
            $nom = str_replace("'","\'",$c->getName());
            $nom = str_replace("/","\/",$c->getName());
            $nom = str_replace("(","\(",$c->getName());
            $nom = str_replace(")","\)",$c->getName());
            $nom = str_replace(","," ou ",$c->getName());
            
            //récupération du nom de la catégorie à dupliquer
            $typeNom = $c->getType()->getNom();
            
            $newType = $typeRepository->findFromConcoursAndType($concours,$typeNom);
            $sql = '
                INSERT INTO categorie (type_id,ordre,name,concours_id,participe) VALUES ('.$newType[0]->getId().','.$c->getOrdre().','.'"'.$nom.'"'.','.$concours->getId().','.'"'.$c->getParticipe().'"'.')';
                //dd($sql);
            $resultSet = $conn->executeStatement($sql);
            
        }

        //sous-catégories
        //récupération des sous-catégories du concours à dupliquer
        $procedes = $procedeRepository->findFromConcours($concours_a_dupliquer_id);
        foreach($procedes as $p){
            //dd($c);
            $conn = $this->entityManager->getConnection();
            $nom = str_replace("'","\'",$p->getName());
            $nom = str_replace("/","\/",$p->getName());
            $nom = str_replace("(","\(",$p->getName());
            $nom = str_replace(")","\)",$p->getName());
            $nom = str_replace(","," ou ",$p->getName());

            //récupération du nom de la catégorie à dupliquer
            $categorieName = $p->getCategorie()->getName();
    
            $newCategorie = $categorieRepository->findFromConcoursAndCategorie($concours,$categorieName);
            $sql = '
                INSERT INTO procede (categorie_id,name) VALUES ('.$newCategorie[0]->getId().','.'"'.$nom.'"'.')';
                //dd($sql);
            $resultSet = $conn->executeStatement($sql);
            
        }
        

        return $this->redirectToRoute('procede_index');

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
