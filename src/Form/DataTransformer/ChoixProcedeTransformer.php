<?php
namespace App\Form\DataTransformer;

use App\Entity\Procede;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChoixProcedeTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     */
    public function transform($issue): string
    {
        if (null === $issue) {
            return '';
        }

        return $issue->getId();
    }

    /**
     * Transforms a string (number) to an object (procede).
     *
     * @param  string $choixprocede
     * @throws TransformationFailedException if object (procede) is not found.
     */
    public function reverseTransform($choixprocede): ?Procede
    {
        dd($choixprocede);
        // no procede number? It's optional, so that's ok
        if (!$choixprocede) {
            return '';
        }

        $procede = $this->entityManager
            ->getRepository(Procede::class)
            // query for the Procede with this id
            ->find($choixprocede)
        ;

        if (null === $choixprocede) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An procede with number "%s" does not exist!',
                $choixprocede
            ));
        }

        return $procede;
    }
}