<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NbreMaxEch extends Constraint
{
    public $message = "Vous avez déjà atteint le nombre maximal d'échantillons dans cette catégorie.";
}