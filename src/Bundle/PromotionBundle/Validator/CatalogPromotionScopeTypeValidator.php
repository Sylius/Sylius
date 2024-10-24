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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScopeType;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CatalogPromotionScopeTypeValidator extends ConstraintValidator
{
    /** @param array<array-key, string> $scopeTypes */
    public function __construct(private array $scopeTypes)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CatalogPromotionScopeType) {
            throw new UnexpectedTypeException($constraint, CatalogPromotionScopeType::class);
        }

        if (!$value instanceof CatalogPromotionScopeInterface) {
            throw new UnexpectedTypeException($value, CatalogPromotionScopeInterface::class);
        }

        $type = $value->getType();
        if (null === $type || '' === $type) {
            return;
        }

        if (!in_array($type, $this->scopeTypes, true)) {
            $this->context->buildViolation($constraint->invalidType)
                ->setParameter('{{ available_scope_types }}', implode(', ', $this->scopeTypes))
                ->atPath('type')
                ->addViolation()
            ;
        }
    }
}
