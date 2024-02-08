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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ForProductsScopeValidatorSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);
    }

    function it_is_a_scope_validator(): void
    {
        $this->shouldHaveType(ScopeValidatorInterface::class);
    }

    function it_adds_violation_if_catalog_promotion_scope_has_not_existing_products_configured(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $productRepository->findOneBy(['code' => 'not_existing_product'])->willReturn(null);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_products.invalid_products')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.products')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['products' => ['not_existing_product']], new CatalogPromotionScope(), $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        ProductRepositoryInterface $productRepository,
        ExecutionContextInterface $executionContext,
        ProductInterface $product,
    ): void {
        $productRepository->findOneBy(['code' => 'product'])->willReturn($product);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_products.not_empty')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_products.invalid_products')->shouldNotBeCalled();

        $this->validate(['products' => ['product']], new CatalogPromotionScope(), $executionContext);
    }
}
