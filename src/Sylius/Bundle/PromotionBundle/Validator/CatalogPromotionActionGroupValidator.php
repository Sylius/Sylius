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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionGroup;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CatalogPromotionActionGroupValidator extends ConstraintValidator
{
    /** @param array<string, array<string, string>> $validationGroups */
    public function __construct(private array $validationGroups)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CatalogPromotionActionGroup) {
            throw new UnexpectedTypeException($constraint, CatalogPromotionActionGroup::class);
        }

        if (!$value instanceof CatalogPromotionActionInterface) {
            throw new UnexpectedTypeException($value, CatalogPromotionActionInterface::class);
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
            new CatalogPromotionAction(groups: $constraint->groups),
            $groups ?? $constraint->groups,
        );
    }
}
