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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionRuleGroup;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class PromotionRuleGroupValidator extends ConstraintValidator
{
    /** @param array<string, array<string, string>> $validationGroups */
    public function __construct(private array $validationGroups)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PromotionRuleGroup) {
            throw new UnexpectedTypeException($constraint, PromotionRuleGroup::class);
        }

        if (!$value instanceof PromotionRuleInterface) {
            throw new UnexpectedValueException($value, PromotionRuleInterface::class);
        }

        /** @var string[] $groups */
        $groups = $this->validationGroups[$value->getType()] ?? $constraint->groups;
        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate(value: $value, groups: $groups);
    }
}
