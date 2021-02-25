<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class CheckVolumeValidator extends ConstraintValidator
{
    
    public function validate($echantillon, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckVolume */

        if (null === $echantillon->getVolume() || '' === $echantillon->getVolume()) {
            return;
        }

        $minVol = $echantillon->getCategorie()->getType()->getVolMinLot();
        if($echantillon->getVolume() < $minVol){
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $echantillon->getVolume())
            ->addViolation();
        }
    }
}
