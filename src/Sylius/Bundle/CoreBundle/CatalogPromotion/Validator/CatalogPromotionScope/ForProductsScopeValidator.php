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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
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

        foreach ($configuration['products'] as $productCode) {
            if (null === $this->productRepository->findOneBy(['code' => $productCode])) {
                $context
                    ->buildViolation('sylius.catalog_promotion_scope.for_products.invalid_products')
                    ->atPath('configuration.products')
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
