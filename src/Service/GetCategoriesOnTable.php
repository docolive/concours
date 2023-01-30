<?php
namespace App\Service;

use App\Service\ConcoursSession;
use App\Repository\TableRepository;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class GetCategoriesOnTable
{
    private $session;
    private $categorieRepository;
    private $tableRepository;
    private $echantillonRepository;
    private $entityManagerInterface;

    public function __construct(ConcoursSession $concoursSession, CategorieRepository $categorieRepository, TableRepository $tableRepository, EchantillonRepository $echantillonRepository, EntityManagerInterface $entityManagerInterface){
        $this->session = $concoursSession;
        $this->categorieRepository = $categorieRepository;
        $this->tableRepository = $tableRepository;
        $this->echantillonRepository = $echantillonRepository;
        $this->entityManagerInterface = $entityManagerInterface;
    } 
    
    public function categories($concours){
        
        //récupération des tables
        $tables = $this->tableRepository->findAllConcours($concours);
        //catégories ouvertes
        $categos = $this->categorieRepository->findFromConcoursForTables($concours);

        //échantillons de catégories ouvertes
        $echantillons = $this->echantillonRepository->findEchConcoursForTables($concours);
        // dd($echantillons);

        //données pour la vérification du nombre de catégories  et d'échantillons sur table
        $categories = array();
        $totEchs = 0;
        foreach($tables as $t){
            if(!in_array($t->getCategorie()->getId(),$categories)){
                $categories[] = $t->getCategorie()->getId();
            }
            $totEchs += $t->getMaxEchs();
        }
        //dump($categos);
        //dd($categories);
        return array(
            'categories' => $categories,
            'categos' => $categos,
            'echantillons' => $echantillons,
            'totEchs' => $totEchs
            )
        ;
    }

    public function generateCode($alphabet,$tableName, $testano){
        $lettre1 = $alphabet[rand(0,25)];
        $lettre2 = $alphabet[rand(0,25)];
        $number = rand(0,9);
        $codAno = $tableName.'-'.$lettre1.$lettre2;

        if(!in_array($codAno,$testano)){
            $testano[] = $codAno;
        }else{
            $this->generateCode($alphabet,$tableName,$testano);
        }
        return array(
            'testano' => $testano,
            'codAno' => $codAno
        )
            ;
    }

    public function shuffleOnTables($tabIdEchs,$categorie,$alphabet,$testano){
        //tableau candidat / table /echantillon
        $tabCandTab = array();

        //on mélange les échantillons
        shuffle($tabIdEchs);

        $c = 0;

        foreach($categorie->getTables() as $table){
            $tableName = $table->getName();
                
            for($i = 0; $i <= $table->getMaxEchs() - 1; $i++){
                if(isset($tabIdEchs[$i + $c])){
                    $ech = $this->echantillonRepository->find($tabIdEchs[$i + $c]);

                    if(!in_array($tableName.'-'.$table->getId().'-'.$ech->getUser()->getId(),$tabCandTab)){
                        $tabCandTab[$ech->getId()] = $tableName.'-'.$table->getId().'-'.$ech->getUser()->getId();
                    }
                    // else{
                    //     //dump($tabCandTab);
                    //     //return $tableName.'-'.$table->getId().'-'.$ech->getUser()->getId();
                    //     $this->shuffleOnTables($tabIdEchs,$categorie,$alphabet,$testano);
                    // }

                }
            }
            $c = $i + $c;
            
        }
        //return 'ici';
        // return $tabCandTab;
        $testano = array();
            foreach($tabCandTab as $k => $t){
                // dump($t);
                $tableName = explode('-',$t)[0];
                $tableId = explode('-',$t)[1];
                // dump($tableName);
                $ech = $this->echantillonRepository->find($k);
                // dump($ech);
                $codAno = $this->generateCode($alphabet,$tableName,$testano)['codAno'];
                $testano = $this->generateCode($alphabet,$tableName,$testano)['testano'];
                // dump($codAno);
                $ech->setCode($codAno);

                $tableOK = $this->tableRepository->find($tableId);
                $ech->setTableJury($tableOK);
                // dump($ech);
                $this->entityManagerInterface->persist($ech);
                $this->entityManagerInterface->flush();

            }
        //dd($testano);
        return $testano;
    }
        
}