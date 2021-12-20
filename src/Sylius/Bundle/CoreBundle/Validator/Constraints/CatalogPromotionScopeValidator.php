<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionScopeValidator extends ConstraintValidator
{
    private array $scopeTypes;

    private array $scopeValidators;

    public function __construct(array $scopeTypes, iterable $scopeValidators)
    {
        $this->scopeTypes = $scopeTypes;
        $this->scopeValidators = $scopeValidators instanceof \Traversable ? iterator_to_array($scopeValidators) : $scopeValidators;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        /** @var CatalogPromotionScopeInterface $value */
        if (!in_array($value->getType(), $this->scopeTypes, true)) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();

            return;
        }

        $type = $value->getType();
        if (!key_exists($type, $this->scopeValidators)) {
            return;
        }

        $configuration = $value->getConfiguration();

        /** @var ScopeValidatorInterface $validator */
        $validator = $this->scopeValidators[$type];
        $validator->validate($configuration, $constraint, $this->context);
    }
}
