<?php

namespace App\Validator;

use finfo;
use App\Repository\EchantillonRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class CheckNbreEchValidator extends ConstraintValidator
{
    private $repo;
    public function __construct(EchantillonRepository $echantillonRepository)
    {
        $this->repo = $echantillonRepository;
    }
    
    public function validate($echantillon, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckNbreEch */

        if (null === $echantillon->getLot() || '' === $echantillon->getLot()) {
            return;
        }
        if($echantillon->getCategorie()->getType()->getOtable()){
            $maxEch = $echantillon->getCategorie()->getType()->getNbreMaxEch();
            $echs = $this->repo->findEchMemeTypeOT($echantillon->getUser(),$echantillon->getVariety(),$echantillon->getCategorie());
            

        }else{
            $maxEch = $echantillon->getCategorie()->getType()->getNbreMaxEch();
            $echs = $this->repo->findEchMemeCategorie($echantillon->getUser(),$echantillon->getCategorie());
        }
        $lim = count($echs) + 1;
        
        if($lim > $maxEch){
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ lim }}', $maxEch)
            ->atPath('categorie')
            ->addViolation();
        }
    }
}
