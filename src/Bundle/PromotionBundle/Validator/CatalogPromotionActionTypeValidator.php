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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionType;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScopeType;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CatalogPromotionActionTypeValidator extends ConstraintValidator
{
    /** @param array<array-key, string> $actionTypes */
    public function __construct(private array $actionTypes)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CatalogPromotionActionType) {
            throw new UnexpectedTypeException($constraint, CatalogPromotionScopeType::class);
        }

        if (!$value instanceof CatalogPromotionActionInterface) {
            throw new UnexpectedTypeException($value, CatalogPromotionActionInterface::class);
        }

        $type = $value->getType();
        if (null === $type || '' === $type) {
            return;
        }

        if (!in_array($type, $this->actionTypes, true)) {
            $this->context->buildViolation($constraint->invalidType)
                ->setParameter('{{ available_action_types }}', implode(', ', $this->actionTypes))
                ->atPath('type')
                ->addViolation()
            ;
        }
    }
}
