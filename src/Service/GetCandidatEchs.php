<?php
namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Echantillon;
use App\Repository\EchantillonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GetCandidatEchs
{
    private $concours;
    private $user;
    private $echantillonRepository;

    public function __construct(ConcoursSession $concoursSession, Security $security, EchantillonRepository $echantillonRepository)
    {
        $this->concours = $concoursSession->recup();
        $this->user = $security->getUser();
        $this->echantillonRepository = $echantillonRepository;
    }
    
    public function getEchs(){
        
        return $this->echantillonRepository->findAllEchUser($this->user,$this->concours);

    }

    public function getAdminEchs($user){
        
        return $this->echantillonRepository->findAllEchUser($user,$this->concours);

    }
    
}