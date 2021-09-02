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

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionRulesValidator extends ConstraintValidator
{
    private ProductVariantRepositoryInterface $variantRepository;

    public function __construct(ProductVariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionRules $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionRules::class);
        /** @var CatalogPromotionRuleInterface $rule */
        foreach ($value as $rule) {
            if ($rule->getType() !== CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS) {
                $this->context->addViolation($constraint->invalidType);

                continue;
            }

                $this->validateRuleConfiguration($rule->getConfiguration(), $constraint);
        }
    }

    private function validateRuleConfiguration(array $configuration, CatalogPromotionRules $constraint): void
    {
        foreach ($configuration as $variantCode) {
            if (null === $this->variantRepository->findOneBy(['code' => $variantCode])) {
                $this->context->addViolation($constraint->invalidConfiguration);

                break;
            }
        }
    }
}
