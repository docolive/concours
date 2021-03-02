<?php
namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Procede;
use App\Repository\ProcedeRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetProcedes
{
    private $repo;
   
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em,ProcedeRepository $procedeRepository)
    {
        $this->em = $em;
        $this->repo = $procedeRepository;

    }
    
    public function countProcede($categorieId)
    {
        $procedes = 0;
        
        if(!is_null($categorieId)){

            $procedes = count($this->repo->findProcedes($categorieId));
        }
        return $procedes;
    }
}