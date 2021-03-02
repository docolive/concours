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
    public $message = "Merci de choisir le procédé de fabrication dans la liste ci-dessous.";
}