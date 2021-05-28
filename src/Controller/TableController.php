<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Form\TableEditType;
use App\Service\ConcoursSession;
use App\Repository\TableRepository;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/admin")
* @IsGranted("ROLE_ADMIN")
*/
class TableController extends AbstractController
{
    private $session;
    private $categorieRepository;
    private $tableRepository;
    private $echantillonRepository;

    public function __construct(ConcoursSession $concoursSession, CategorieRepository $categorieRepository, TableRepository $tableRepository, EchantillonRepository $echantillonRepository){
        $this->session = $concoursSession;
        $this->categorieRepository = $categorieRepository;
        $this->tableRepository = $tableRepository;
        $this->echantillonRepository = $echantillonRepository;
    } 

    /**
     * @Route("/table/anonymat", name="table-anonymat")
     */
    public function anonymat(Request $request): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        //récupération des tables
        $tables = $this->tableRepository->findAllConcours($concours);
        $categos = $this->categorieRepository->findFromConcours($concours);
        $echantillons = $this->echantillonRepository->findEchConcours($concours);
        
        //vérification du nombre de catégories  et d'échantillons sur table
        $categories = array();
        $totEchs = 0;
        foreach($tables as $t){
            if(!in_array($t->getCategorie()->getId(),$categories)){
                $categories[] = $t->getCategorie()->getId();
            }
            $totEchs += $t->getMaxEchs();
        }
        if(count($categories) != count($categos)){
            $this->addFlash(
                'error',
                'Le nombre total de catégories affectées aux tables ne correspond pas au nombre total de catégories du concours !'
            );
            return $this->redirectToRoute('table-index');
        }

        if(count($echantillons) != $totEchs){
            $this->addFlash(
                'error',
                'Le nombre total d\'échantillons affectés aux tables ne correspond pas au nombre total des échantillons du concours !'
            );
            return $this->redirectToRoute('table-index');
        }

        //répartition par table et anonymat
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        foreach($categories as $c){
            $categorie = $this->categorieRepository->find($c);
            if(count($categorie->getTables()) > 0){
                //Sil n'y a qu'une seule table pour la catégorie
                //on anonymise les échantillons
                if(count($categorie->getTables()) == 1){
                    foreach($categorie->getTables() as $table){
                        $tableName = $table->getName();
                    }
                    $echantillons = $categorie->getEchantillons();
                    foreach($echantillons as $ech){
                        //dd($ech);
                        $lettre1 = $alphabet[rand(0,25)];
                        $lettre2 = $alphabet[rand(0,25)];
                        $number = rand(0,9);
                        $codAno = $tableName.'-'.$lettre1.$lettre2.$number;
                        //dump($codAno);
                        $ech->setCode($codAno);
                        $ech->setTableJury($table);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ech);
                        $em->flush();
                    }

                }else{
                //s'il y a plusieurs tables  
                //répartir les échantillons sur les tables
                $nbreTables = count($categorie->getTables());
                $echantillons = $categorie->getEchantillons();
                $totEchs = count($echantillons);
                $tabIdEchs = [];
                foreach($echantillons as $e){
                    $tabIdEchs[] = $e->getId();
                }
                shuffle($tabIdEchs);
                $c = 0;
                foreach($categorie->getTables() as $table){
                    $tableName = $table->getName();
                    
                    for($i = 0; $i <= $table->getMaxEchs() - 1; $i++){
                        $ech = $this->echantillonRepository->find($tabIdEchs[$i + $c]);
                        $lettre1 = $alphabet[rand(0,25)];
                        $lettre2 = $alphabet[rand(0,25)];
                        $number = rand(0,9);
                        $codAno = $tableName.'-'.$lettre1.$lettre2.$number;
                        //dump($ech->getId());
                        //dump($codAno);
                        $ech->setCode($codAno);
                        $ech->setTableJury($table);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($ech);
                        $em->flush();

                    }
                    $c = $i + $c;
                }
                }
            }
        }
        return $this->redirectToRoute('table-index');


    }
    
    /**
     * @Route("/table/add", name="table-add")
     */
    public function add(Request $request): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        $table = new Table();

        //lister uniquement les catégories du concours
        $categories = $this->categorieRepository->findFromConcours($concours);
        
        $form = $this->createForm(TableType::class,$table,['choice_categories' => $categories]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($table);
            $em->flush();
            return $this->redirectToRoute('table-index');
        }

        return $this->render('table/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/table/edit/{id}", name="table-edit")
     */
    public function edit(Request $request, $id): Response
    {
        $concours = $this->session->recup();
        $table = $this->tableRepository->find($id);

        //lister uniquement les catégories du concours
        $categories = $this->categorieRepository->findFromConcours($concours);
        
        $form = $this->createForm(TableEditType::class,$table,['choice_categories' => $categories]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($table);
            $em->flush();
            return $this->redirectToRoute('table-index');
        }

        return $this->render('table/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/table/index", name="table-index")
     */
    public function index(): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $tables = $this->tableRepository->findAllConcours($concours);
        return $this->render('table/index.html.twig', [
            'tables' => $tables,
        ]);
    }

    /**
     * @Route("/table/delete/{id}", name="table-delete")
     */
    public function delete($id): Response
    {
        $table = $this->tableRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($table);
        $em->flush();
        return $this->redirectToRoute('table-index');
    }

}
