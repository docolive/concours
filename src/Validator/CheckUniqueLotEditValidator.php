<?php

namespace App\Validator;

use App\Service\GetCandidatEchs;
use App\Validator\CheckUniqueLotEdit;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\Security;


class CheckUniqueLotEditValidator extends ConstraintValidator
{
    private $getCandidatEchs;

    public function __construct(GetCandidatEchs $getCandidatEchs, Security $security)
    {
        $this->getCandidatEchs = $getCandidatEchs;
        $this->security = $security;
    }
    public function checkUniqueLots($value)
    {
        $user = $this->security->getUser();
        $roles = $user->getRoles();
        if(in_array('ROLE_ADMIN',$roles) || in_array('ROLE_SUPER_ADMIN',$roles)){
            $echs = $this->getCandidatEchs->getAdminEchs($value->getUser());
        }else{

            $echs = $this->getCandidatEchs->getEchs();
        }
        
        //dd($echs);
        $tabLots = array();
        foreach($echs as $e){
            $tabLots[$e->getId()] = $e->getLot();
        }
        //dump($tabLots);
        $k = array_keys($tabLots,$value->getLot());
        //dd($k);
            if(count($k) == 1){
                return true;
            }else{
                return false;
            }
    }

        public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckVariety */
        $uniqueLot = $this->checkUniqueLots($value);
        if($uniqueLot == false){
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value->getLot())
            ->atPath('lot')
            ->addViolation();
        }
    }
    }

