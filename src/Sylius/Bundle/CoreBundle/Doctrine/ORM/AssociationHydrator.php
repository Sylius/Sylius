<?php

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class AssociationHydrator
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param EntityManager $entityManager
     * @param ClassMetadata $classMetadata
     */
    public function __construct(EntityManager $entityManager, ClassMetadata $classMetadata)
    {
        $this->entityManager = $entityManager;
        $this->classMetadata = $classMetadata;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param mixed $subjects
     * @param array|string[] $associationsPaths
     */
    public function hydrateAssociations($subjects, array $associationsPaths)
    {
        foreach ($associationsPaths as $associationPath) {
            $this->hydrateAssociation($subjects, $associationPath);
        }
    }

    /**
     * @param mixed $subjects
     * @param string $associationPath
     */
    public function hydrateAssociation($subjects, $associationPath)
    {
        $initialAssociations = explode('.', $associationPath);
        $finalAssociation = array_pop($initialAssociations);
        $subjects = $this->normalizeSubject($subjects);

        $classMetadata = $this->classMetadata;
        foreach ($initialAssociations as $initialAssociation) {
            $subjects = array_reduce($subjects, function (array $accumulator, $subject) use ($initialAssociation) {
                $subject = $this->propertyAccessor->getValue($subject, $initialAssociation);

                return array_merge($accumulator, $this->normalizeSubject($subject));
            }, []);

            $classMetadata = $this->entityManager->getClassMetadata($classMetadata->getAssociationTargetClass($initialAssociation));
        }

        $this->entityManager->createQueryBuilder()
            ->select('PARTIAL subject.{id}')
            ->addSelect('associations')
            ->from($classMetadata->name, 'subject')
            ->leftJoin(sprintf('subject.%s', $finalAssociation), 'associations')
            ->where('subject IN (:subjects)')
            ->setParameter('subjects', $subjects)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param mixed $subject
     *
     * @return array
     */
    private function normalizeSubject($subject)
    {
        if ($subject instanceof Collection) {
            return $subject->toArray();
        }

        if (!is_array($subject)) {
            return [$subject];
        }

        return $subject;
    }
}
