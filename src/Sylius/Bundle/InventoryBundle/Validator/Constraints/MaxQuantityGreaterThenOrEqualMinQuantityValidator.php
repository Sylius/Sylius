<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 *
 * @author Myke Hines <myke@webhines.com>
 */
class MaxQuantityGreaterThenOrEqualMinQuantityValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if ($entity->isManageStock() && $entity->getMaxQuantityInCart() < $entity->getMinQuantityInCart())
    		$this->context->addViolationAt('maxQuantityInCart', $constraint->message);

    }
}
