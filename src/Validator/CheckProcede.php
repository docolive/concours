<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CheckProcede extends Constraint
{
    public function getTargets()
        {
            return self::CLASS_CONSTRAINT;
        }
    public $message = "Merci de choisir la sous-catégorie dans la liste ci-dessous.";
}