<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Webmozart\Assert\Assert;

final class HasEnabledEntityValidator extends ConstraintValidator
{
    /** @var ManagerRegistry */
    private $registry;

    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws ConstraintDefinitionException
     */
    public function validate($entity, Constraint $constraint): void
    {
        /** @var HasEnabledEntity $constraint */
        Assert::isInstanceOf($constraint, HasEnabledEntity::class);

        $enabled = $this->accessor->getValue($entity, $constraint->enabledPath);

        if ($enabled === true) {
            return;
        }

        $objectManager = $this->getProperObjectManager($constraint->objectManager, $entity);

        $this->ensureEntityHasProvidedEnabledField($objectManager, $entity, $constraint->enabledPath);

        $criteria = [$constraint->enabledPath => true];

        $repository = $objectManager->getRepository(get_class($entity));
        $results = $repository->{$constraint->repositoryMethod}($criteria);

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($results instanceof \Iterator) {
            $results->rewind();
        } elseif (is_array($results)) {
            reset($results);
        }

        if ($this->isLastEnabledEntity($results, $entity)) {
            $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $constraint->enabledPath;

            $this->context->buildViolation($constraint->message)->atPath($errorPath)->addViolation();
        }
    }

    /**
     * If no entity matched the query criteria or a single entity matched, which is the same as the entity being
     * validated, the entity is the last enabled entity available.
     *
     * @param array|\Iterator $result
     * @param object $entity
     */
    private function isLastEnabledEntity($result, $entity): bool
    {
        return !$result || 0 === count($result)
        || (1 === count($result) && $entity === ($result instanceof \Iterator ? $result->current() : current($result)));
    }

    /**
     * @param object $entity
     */
    private function getProperObjectManager(?string $manager, $entity): ?ObjectManager
    {
        if ($manager) {
            $objectManager = $this->registry->getManager($manager);

            $this->validateObjectManager($objectManager, sprintf('Object manager "%s" does not exist.', $manager));
        } else {
            $objectManager = $this->registry->getManagerForClass(get_class($entity));

            $this->validateObjectManager(
                $objectManager,
                sprintf(
                    'Unable to find the object manager associated with an entity of class "%s".',
                    get_class($entity)
                )
            );
        }

        return $objectManager;
    }

    /**
     * @throws ConstraintDefinitionException
     */
    private function validateObjectManager(?ObjectManager $objectManager, string $exceptionMessage): void
    {
        if (!$objectManager) {
            throw new ConstraintDefinitionException($exceptionMessage);
        }
    }

    /**
     * @param object $entity
     *
     * @throws ConstraintDefinitionException
     */
    private function ensureEntityHasProvidedEnabledField(ObjectManager $objectManager, $entity, string $enabledPropertyPath): void
    {
        /** @var ClassMetadata $class */
        $class = $objectManager->getClassMetadata(get_class($entity));

        if (!$class->hasField($enabledPropertyPath) && !$class->hasAssociation($enabledPropertyPath)) {
            throw new ConstraintDefinitionException(
                sprintf("The field '%s' is not mapped by Doctrine, so it cannot be validated.", $enabledPropertyPath)
            );
        }
    }
}
