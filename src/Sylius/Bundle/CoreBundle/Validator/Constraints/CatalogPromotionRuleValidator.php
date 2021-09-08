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

final class CatalogPromotionRuleValidator extends ConstraintValidator
{
    private ProductVariantRepositoryInterface $variantRepository;

    public function __construct(ProductVariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionRule $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionRule::class);

        /** @var CatalogPromotionRuleInterface $value */
        if ($value->getType() !== CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();

            return;
        }

        $configuration = $value->getConfiguration();
        if (!array_key_exists('variants', $configuration) || empty($configuration['variants'])) {
            $this->context->buildViolation($constraint->notEmpty)->atPath('configuration.variants')->addViolation();

            return;
        }

        foreach ($configuration['variants'] as $variantCode) {
            if (null === $this->variantRepository->findOneBy(['code' => $variantCode])) {
                $this->context->buildViolation($constraint->invalidVariants)->atPath('configuration.variants')->addViolation();

                break;
            }
        }
    }
}
