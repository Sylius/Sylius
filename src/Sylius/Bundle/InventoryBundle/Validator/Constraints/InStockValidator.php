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
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InStockValidator extends ConstraintValidator
{
    protected $availabilityChecker;

    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        if ($this->availabilityChecker->isStockSufficient($value->getVariant(), $value->getQuantity())) {
            return;
        }

        $this->context->addViolation(
            $constraint->message,
            array('%product%' => $value->getProduct()->getName())
        );
    }
}
