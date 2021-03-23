<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ChecKSiret extends Constraint
{
    public function getTargets()
        {
            return self::CLASS_CONSTRAINT;
        }
    public $message = 'Numéro de SIRET "{{ value }}" non valide';
}
