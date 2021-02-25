<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckVolume extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = '"{{ value }}" est inférieur au volume minimum pour cette catégorie.';
    public function getTargets()
{
    return self::CLASS_CONSTRAINT;
}
}
