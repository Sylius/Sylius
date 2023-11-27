<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionAction;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class PromotionActionValidator extends ConstraintValidator
{
    /** @param array<string, string> $actionTypes */
    public function __construct(private array $actionTypes)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PromotionAction) {
            throw new UnexpectedTypeException($constraint, PromotionAction::class);
        }

        if (!$value instanceof PromotionActionInterface) {
            throw new UnexpectedValueException($value, PromotionActionInterface::class);
        }

        $type = $value->getType();
        if (!array_key_exists($type, $this->actionTypes)) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();
        }
    }
}
