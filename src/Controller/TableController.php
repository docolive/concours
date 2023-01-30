<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Form\TableEditType;
use App\Service\GetCategories;
use App\Service\ConcoursSession;
use App\Repository\TableRepository;
use App\Service\GetCategoriesOnTable;
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
    private $getCategories;

    public function __construct(ConcoursSession $concoursSession, CategorieRepository $categorieRepository, TableRepository $tableRepository, EchantillonRepository $echantillonRepository, GetCategoriesOnTable $getCategories){
        $this->session = $concoursSession;
        $this->categorieRepository = $categorieRepository;
        $this->tableRepository = $tableRepository;
        $this->echantillonRepository = $echantillonRepository;
        $this->getCategories = $getCategories;
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

        $datas = $this->getCategories->categories($concours);

        $categories = $datas['categories'];
        $categos = $datas['categos'];
        $echantillons = $datas['echantillons'];
        $totEchs = $datas['totEchs'];
        
        if(count($categories) != count($categos)){
            $this->addFlash(
                'error',
                'Le nombre total de catégories affectées aux tables ('.count($categories).') ne correspond pas au nombre total de catégories du concours ouvertes ('.count($categos).') !'
            );
            return $this->redirectToRoute('table-index');
        }
        //dump(count($echantillons));
        //dd($totEchs);
        if(count($echantillons) != $totEchs){
            $this->addFlash(
                'error',
                'Le nombre total d\'échantillons affectés aux tables ne correspond pas au nombre total des échantillons du concours !'
            );
            return $this->redirectToRoute('table-index');
        }

        return $this->redirectToRoute('table-index');
    }

    /**
     * @Route("/table/anonymisation", name="table-anonymisation")
     */
    function anonymisation(){
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        //répartition par table et anonymat
        $alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        //catégories ouvertes
        $datas = $this->getCategories->categories($concours);
        $categories = $datas['categories'];
        
        //testano est le tableau qui vérifie l'unicité du code anonymt
        $testano = array();

    foreach($categories as $c){
        $categorie = $this->categorieRepository->find($c);
        if($categorie->getId() !=41){
            
        
        //dump($categorie);
        //si la catégorie est bien affectée au moins à une table
        if(count($categorie->getTables()) > 0){
            //S'il n'y a qu'une seule table pour la catégorie
            //on anonymise les échantillons
            if(count($categorie->getTables()) == 1){
                foreach($categorie->getTables() as $table){
                    $tableName = $table->getName();
                }
                $echantillons = $categorie->getEchantillons();
                foreach($echantillons as $ech){
                    //dd($ech);
                    $codAno = $this->getCategories->generateCode($alphabet,$tableName,$testano)['codAno'];
                    $testano = $this->getCategories->generateCode($alphabet,$tableName,$testano)['testano'];
                    //dump($codAno);
                    
                    $ech->setCode($codAno);
                    $ech->setTableJury($table);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($ech);
                   // dump($em->persist($ech));
                    $em->flush();
                }

            }else{
            //s'il y a plusieurs tables  
            //répartir les échantillons sur les tables
            $tables = $categorie->getTables();
            
            $echantillons = $categorie->getEchantillons();

            $tabIdEchs = [];

            foreach($echantillons as $e){
                $tabIdEchs[] = $e->getId();
            }

            shuffle($tabIdEchs);

            //répartition des échantillons sur les tables 
            $tabEchs = array();

            $echId = 0;

            foreach($tables as $t){
                //dd($t);
                //soit $et = nbre d'échantillons sur la table
                $et = 0;

                while($et <= $t->getMaxEchs() - 1){
                    if(isset($tabIdEchs[$echId])){
                    $tabEchs[$t->getId()][] = $tabIdEchs[$echId];
                    //dd($tabEchs);
                    $et++;
                    $echId++;
                    }
                }
            }
            //dd($tabEchs);
            $testano = array();
            $listechs = array();
            // $listetab = array();
            $i = 0;
            foreach($tabEchs as $k => $echIds){
                //dump($k);
               // dump($echIds);
                foreach($echIds as $echId){
                    //dd($echId);
                    $table = $this->tableRepository->find($k);
                    //dump($table);
                    $tableName = $table->getName();
                     $codAno = $this->getCategories->generateCode($alphabet,$tableName,$testano)['codAno'];
                     $testano = $this->getCategories->generateCode($alphabet,$tableName,$testano)['testano'];
                    // $codAno = 'OOO';
                    $echantillon = $this->echantillonRepository->find($echId);
                    
                    $listechs[]=$echantillon;
                    $listetab[$i]['table'] = $table->getId();
                    $listetab[$i]['code'] = $codAno;
                    $listetab[$i]['echantillon'] = $echantillon->getId();
                    $i++;
                }
                // dd($listetab);
            }
            
                // dump($testano);
                //dd($listetab);
                //dd($listechs);
                $em = $this->getDoctrine()->getManager();
            
                foreach($listetab as $l){
                    $echantillontab = $this->echantillonRepository->find($l['echantillon']);
                    $echantillontab->setTableJury($this->tableRepository->find($l['table']));
                    $echantillontab->setCode($l['code']);
                    // dump($echantillontab);
                    $em->flush();
                    $em->clear();
                }
            }
        
        }
        //dd('ici');
        
        // $em->flush();
        // dump('flush');
    }
    }
    
    
    //dd($tabEchs);
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
            //dd($table);
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
        $em = $this->getDoctrine()->getManager();

        $table = $this->tableRepository->find($id);
        //suppression des échantillons de la table
        $echs = $table->getEchantillons();
        foreach($echs as $e){
            //dd($e);
            $e->setTableJury(null);
            $em->persist($e);
            $em->flush();
        }
       
        $em->remove($table);
        $em->flush();
        return $this->redirectToRoute('table-index');
    }

}
