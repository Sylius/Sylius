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

namespace Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope;

use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class ForVariantsScopeValidator implements ScopeValidatorInterface
{
    private ProductVariantRepositoryInterface $variantRepository;

    public function __construct(ProductVariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
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
