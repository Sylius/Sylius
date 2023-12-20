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

namespace Sylius\Bundle\ShippingBundle\Validator;

use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodRule;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ShippingMethodRuleValidator extends ConstraintValidator
{
    /**
     * @param array<string, string> $ruleTypes
     * @param array<string, array<string, string>> $validationGroups
     */
    public function __construct(
        private array $ruleTypes,
        private array $validationGroups,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ShippingMethodRuleInterface) {
            throw new UnexpectedValueException($value, ShippingMethodRuleInterface::class);
        }

        if (!$constraint instanceof ShippingMethodRule) {
            throw new UnexpectedTypeException($constraint, ShippingMethodRule::class);
        }

        $type = $value->getType();
        if (!array_key_exists($type, $this->ruleTypes)) {
            $this->context->buildViolation($constraint->invalidType)
                ->setParameter('{{ available_rule_types }}', implode(', ', array_keys($this->ruleTypes)))
                ->atPath('type')
                ->addViolation()
            ;

            return;
        }

        /** @var string[] $groups */
        $groups = $this->validationGroups[$type] ?? $constraint->groups;
        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate(value: $value, groups: $groups);
    }
}
