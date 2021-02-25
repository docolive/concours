<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class CheckVarietyValidator extends ConstraintValidator
{
    
    public function validate($echantillon, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckVariety */

        if($echantillon->getCategorie()->getType()->getOtable() && $echantillon->getVariety() == ''){
        $this->context->buildViolation($constraint->message)
            //->setParameter('{{ value }}', $echantillon->getVolume())
            ->atPath('variety')
            ->addViolation();
        }
    }
}
