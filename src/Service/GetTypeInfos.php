<?php
namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Echantillon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class GetTypeInfos
{
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }
    
    public function checkMaxEch($categorieId)
    {
        
        $repository = $this->em->getRepository(Categorie::class);
        $catego = $repository->find($categorieId);
        $type = $catego->getType();
        $maxEch = $type->getNbreMaxEch();

        $user = $this->security->getUser();

        $repo = $this->em->getRepository(Echantillon::class);
        $echs = $repo->findBy(
            array(
                'categorie' => $catego,
                'user' => $user
            )
        );
        
        if((count($echs) + 1) > $maxEch){
            return false;
        }
        
        return true;
    }
}