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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScopeGroup;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CatalogPromotionScopeGroupValidator extends ConstraintValidator
{
    /** @param array<string, array<string, string>> $validationGroups */
    public function __construct(private array $validationGroups)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CatalogPromotionScopeGroup) {
            throw new UnexpectedTypeException($constraint, CatalogPromotionScopeGroup::class);
        }

        if (!$value instanceof CatalogPromotionScopeInterface) {
            throw new UnexpectedTypeException($value, CatalogPromotionScopeInterface::class);
        }

        $type = $value->getType();
        if (null === $type || '' === $type) {
            return;
        }

        $validator = $this->context->getValidator()->inContext($this->context);

        $groups = $this->validationGroups[$type] ?? null;
        if (null !== $groups) {
            $validator->validate(value: $value, groups: $groups);

            if ($this->context->getViolations()->count() > 0) {
                return;
            }
        }

        $validator->validate(
            $value,
            new CatalogPromotionScope(groups: $constraint->groups),
            $groups ?? $constraint->groups,
        );
    }
}
