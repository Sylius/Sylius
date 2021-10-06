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
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionScopeValidator extends ConstraintValidator
{
    private array $scopeValidators;

    public function __construct(iterable $scopeValidators)
    {
        $this->scopeValidators = $scopeValidators instanceof \Traversable ? iterator_to_array($scopeValidators) : $scopeValidators;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        /** @var CatalogPromotionScopeInterface $value */
        if (
            $value->getType() !== CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS &&
            $value->getType() !== CatalogPromotionScopeInterface::TYPE_FOR_TAXONS
        ) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();

            return;
        }

        $configuration = $value->getConfiguration();

        /** @var ScopeValidatorInterface $validator */
        $validator = $this->scopeValidators[$value->getType()];
        $violations = $validator->validate($configuration, $constraint);

        foreach ($violations as $path => $message) {
            $this->context->buildViolation($message)->atPath($path)->addViolation();
        }
    }
}
