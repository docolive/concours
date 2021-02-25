<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckNbreEch extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = "Impossible d'enregistrer cet échantillon car vous dépasseriez le nombre maximal d'échantillons dans cette catégorie";
    public function getTargets()
{
    return self::CLASS_CONSTRAINT;
}
}
