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

use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
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
        if (array_key_exists('variants', $configuration) === false) {
            $this->context->addViolation($constraint->invalidConfiguration);

            return;
        }

        foreach ($configuration['variants'] as $variantCode) {
            if (null === $this->variantRepository->findOneBy(['code' => $variantCode])) {
                $this->context->addViolation($constraint->invalidConfiguration);

                break;
            }
        }
    }
}
