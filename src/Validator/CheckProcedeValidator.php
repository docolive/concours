<?php
namespace App\Validator;

use App\Service\GetProcedes;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CheckProcedeValidator extends ConstraintValidator
{
    private $getProcedes;

    public function __construct(GetProcedes $getProcedes){
        $this->getProcedes = $getProcedes;

    }
    public function validate($echantillon, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\CheckProcede */
        $categorieId = $echantillon->getCategorie()->getId();
        $procedeNecessaire = $this->getProcedes->countProcede($categorieId);

        if($procedeNecessaire > 0 && $echantillon->getProcede() == ''){
        $this->context->buildViolation($constraint->message)
            //->setParameter('{{ value }}', $echantillon->getVolume())
            ->atPath('procede')
            ->addViolation();
        }
    }
}