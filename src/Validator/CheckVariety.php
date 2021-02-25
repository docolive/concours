<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckVariety extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Merci de préciser le nom de la variété.';
    public function getTargets()
{
    return self::CLASS_CONSTRAINT;
}
}
