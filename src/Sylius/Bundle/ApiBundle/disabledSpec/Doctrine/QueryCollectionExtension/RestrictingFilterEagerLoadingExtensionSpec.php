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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductReview;

final class RestrictingFilterEagerLoadingExtensionSpec extends ObjectBehavior
{
    function let(ContextAwareQueryCollectionExtensionInterface $decoratedExtension): void
    {
        $this->beConstructedWith(
            $decoratedExtension,
            [
                Product::class => ['operations' => ['shop_get' => ['enabled' => true]]],
                ProductReview::class => ['operations' => ['shop_get' => ['enabled' => true], 'admin_get' => ['enabled' => false]]],
            ],
        );
    }

    function it_does_nothing_if_current_resource_and_operation_is_restricted(
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, Product::class, 'shop_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldNotBeCalled();
        $this->applyToCollection(...$args);
    }

    public function it_calls_filter_eager_loading_extension_if_current_resource_is_not_restricted(
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, Order::class, 'shop_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldBeCalled();
        $this->applyToCollection(...$args);
    }

    public function it_calls_filter_eager_loading_extension_if_current_operation_is_not_restricted(
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, Product::class, 'admin_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldBeCalled();
        $this->applyToCollection(...$args);
    }

    public function it_calls_filter_eager_loading_extension_if_current_resource_is_restricted_but_operation_is_not(
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, ProductReview::class, 'admin_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldBeCalled();
        $this->applyToCollection(...$args);
    }
}
