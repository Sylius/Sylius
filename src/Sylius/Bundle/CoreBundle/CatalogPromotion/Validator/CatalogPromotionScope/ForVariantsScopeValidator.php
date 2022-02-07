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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\Constraints\CatalogPromotionScope;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class ForVariantsScopeValidator implements ScopeValidatorInterface
{
    public function __construct(
        private ProductVariantRepositoryInterface $variantRepository,
        private SectionProviderInterface $sectionProvider
    ) {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        if (!$this->sectionProvider->getSection() instanceof AdminApiSection) {
            return;
        }

        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        if (!array_key_exists('variants', $configuration) || empty($configuration['variants'])) {
            $context->buildViolation($constraint->variantsNotEmpty)->atPath('configuration.variants')->addViolation();

            return;
        }

        foreach ($configuration['variants'] as $variantCode) {
            if (null === $this->variantRepository->findOneBy(['code' => $variantCode])) {
                $context->buildViolation($constraint->invalidVariants)->atPath('configuration.variants')->addViolation();

                return;
            }
        }
    }
}
