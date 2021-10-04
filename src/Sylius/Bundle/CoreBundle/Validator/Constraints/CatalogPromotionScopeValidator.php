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

use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionScopeValidator extends ConstraintValidator
{
    private ProductVariantRepositoryInterface $variantRepository;

    private TaxonRepositoryInterface $taxonRepository;

    public function __construct(
        ProductVariantRepositoryInterface $variantRepository,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->variantRepository = $variantRepository;
        $this->taxonRepository = $taxonRepository;
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

        if ($value->getType() === CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS) {
            $this->validateForVariantsType($configuration, $constraint);

            return;
        }

        $this->validateForTaxonType($configuration, $constraint);
    }

    private function validateForVariantsType(array $configuration, CatalogPromotionScope $constraint): void
    {
        if (!array_key_exists('variants', $configuration) || empty($configuration['variants'])) {
            $this->context->buildViolation($constraint->variantsNotEmpty)->atPath('configuration.variants')->addViolation();

            return;
        }

        foreach ($configuration['variants'] as $variantCode) {
            if (null === $this->variantRepository->findOneBy(['code' => $variantCode])) {
                $this->context->buildViolation($constraint->invalidVariants)->atPath('configuration.variants')->addViolation();

                break;
            }
        }
    }

    private function validateForTaxonType(array $configuration, CatalogPromotionScope $constraint): void
    {
        if (!isset($configuration['taxons']) || empty($configuration['taxons'])) {
            $this->context->buildViolation($constraint->taxonsNotEmpty)->atPath('configuration.taxons')->addViolation();

            return;;
        }

        foreach ($configuration['taxons'] as $taxonCode) {
            if (null === $this->taxonRepository->findOneBy(['code' => $taxonCode])) {
                $this->context->buildViolation($constraint->invalidTaxons)->atPath('configuration.taxons')->addViolation();

                return;
            }
        }
    }
}
