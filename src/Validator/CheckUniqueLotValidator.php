<?php

namespace App\Validator;

use App\Service\GetCandidatEchs;
use App\Validator\CheckUniqueLot;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckUniqueLotValidator extends ConstraintValidator
{
    private $getCandidatEchs;

    public function __construct(GetCandidatEchs $getCandidatEchs)
    {
        $this->getCandidatEchs = $getCandidatEchs;
    }
    public function checkUniqueLots($value)
    {
        
        $echs = $this->getCandidatEchs->getEchs();
        $tabLots = array();
        foreach($echs as $e){
            $tabLots[] = $e->getLot();
        }
        
        if(in_array($value->getLot(),$tabLots)){
            return false;
        }else{
            return true;
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

