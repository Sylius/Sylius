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

use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/promotion-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0, use the usual symfony logic for validation.',
    CatalogPromotionScopeValidator::class,
);
final class CatalogPromotionScopeValidator extends ConstraintValidator
{
    private array $scopeValidators;

    public function __construct(private array $scopeTypes, iterable $scopeValidators)
    {
        $this->scopeValidators = $scopeValidators instanceof \Traversable ? iterator_to_array($scopeValidators) : $scopeValidators;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        $type = $value->getType();
        if (!array_key_exists($type, $this->scopeValidators)) {
            return;
        }

        $configuration = $value->getConfiguration();

        /** @var ScopeValidatorInterface $validator */
        $validator = $this->scopeValidators[$type];
        $validator->validate($configuration, $constraint, $this->context);
    }
}
