<?php
namespace App\Validator;

use App\Service\GetTypeInfos;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NbreMaxEchValidator extends ConstraintValidator
{
    private $getTypeInfos;

    public function __construct(GetTypeInfos $getTypeInfos){
        $this->getTypeInfos = $getTypeInfos;

    }
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NbreMaxEch) {
            throw new UnexpectedTypeException($constraint, NbreMaxEch::class);
        }
        
        $maxEch = $this->getTypeInfos->checkMaxEch($value);

        if ($maxEch == false) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('erreur', 'value')
                ->addViolation();
        }
    }
}