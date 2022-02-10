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
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class ForProductsScopeValidator implements ScopeValidatorInterface
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionScope $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionScope::class);

        if (!isset($configuration['products']) || empty($configuration['products'])) {
            $context->buildViolation($constraint->productsNotEmpty)->atPath('configuration.products')->addViolation();

            return;
        }

        foreach ($configuration['products'] as $productCode) {
            if (null === $this->productRepository->findOneBy(['code' => $productCode])) {
                $context->buildViolation($constraint->invalidProducts)->atPath('configuration.products')->addViolation();

                return;
            }
        }
    }
}
