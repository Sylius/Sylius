<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/*
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class OneEnabledEntityValidator extends ConstraintValidator
{
    protected $registry;
    protected $accessor;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param object $entity
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof OneEnabledEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\OneEnabledEntity');
        }

        $enabled = $this->accessor->getValue($entity, $constraint->enabledPath);

        if ($enabled === true) {
            return;
        }

        if ($constraint->entityManager) {
            $entityManager = $this->registry->getManager($constraint->entityManager);

            if (!$entityManager) {
                throw new ConstraintDefinitionException(
                    sprintf('Object manager "%s" does not exist.', $constraint->entityManager)
                );
            }
        } else {
            $entityManager = $this->registry->getManagerForClass(get_class($entity));

            if (!$entityManager) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        'Unable to find the object manager associated with an entity of class "%s".',
                        get_class($entity)
                    )
                );
            }
        }

        /* @var $class \Doctrine\Common\Persistence\Mapping\ClassMetadata */
        $class = $entityManager->getClassMetadata(get_class($entity));

        if (!$class->hasField($constraint->enabledPath) && !$class->hasAssociation($constraint->enabledPath)) {
            throw new ConstraintDefinitionException(
                sprintf("The field '%s' is not mapped by Doctrine, so it cannot be validated.", $constraint->enabledPath)
            );
        }

        $criteria = array($constraint->enabledPath => true);

        $repository = $entityManager->getRepository(get_class($entity));
        $result = $repository->{$constraint->repositoryMethod}($criteria);

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof \Iterator) {
            $result->rewind();
        } elseif (is_array($result)) {
            reset($result);
        }

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (!$result || 0 === count($result)
            || (1 === count($result) && $entity === ($result instanceof \Iterator ? $result->current() : current(
                    $result
                )))
        ) {
            $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $constraint->enabledPath;

            $this->context->addViolationAt($errorPath, $constraint->message);
        }
    }
}
