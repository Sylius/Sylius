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

namespace Sylius\Bundle\ResourceBundle\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UniqueWithinCollectionConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($collectionOfEntities, Constraint $constraint)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $collectionOfEntitiesCodes = [];

        foreach ($collectionOfEntities as $key => $entity) {
            $checkingAttribute = $propertyAccessor->getValue($entity, $constraint->attributePath);

            if (null === $checkingAttribute) {
                continue;
            }

            if (!array_key_exists($checkingAttribute, $collectionOfEntitiesCodes)) {
                $collectionOfEntitiesCodes[$checkingAttribute] = $key;
                continue;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->atPath(sprintf('[%d].%s', $key, $constraint->attributePath))
                ->addViolation();

            if (false !== $collectionOfEntitiesCodes[$checkingAttribute]) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath(sprintf('[%d].%s', $collectionOfEntitiesCodes[$checkingAttribute], $constraint->attributePath))
                    ->addViolation();

                $collectionOfEntitiesCodes[$checkingAttribute] = false;
            }
        }
    }
}
