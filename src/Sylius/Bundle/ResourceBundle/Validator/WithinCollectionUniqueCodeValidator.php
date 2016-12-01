<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Validator;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class WithinCollectionUniqueCodeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($collectionOfEntities, Constraint $constraint)
    {
        $collectionOfEntitiesCodes = [];

        /** @var CodeAwareInterface $entity */
        foreach ($collectionOfEntities as $key => $entity) {
            if(null === $entity->getCode()) {
                continue;
            }

            if (!array_key_exists($entity->getCode(), $collectionOfEntitiesCodes)) {
                $collectionOfEntitiesCodes[$entity->getCode()] = $key;
                continue;
            }

            $this->context->buildViolation($constraint->message)->atPath(sprintf('[%d].code', $key))->addViolation();
            if (false !== $collectionOfEntitiesCodes[$entity->getCode()]) {
                $this->context->buildViolation($constraint->message)->atPath(sprintf('[%d].code', $collectionOfEntitiesCodes[$entity->getCode()]))->addViolation();
                $collectionOfEntitiesCodes[$entity->getCode()] = false;
            }
        }
    }
}
