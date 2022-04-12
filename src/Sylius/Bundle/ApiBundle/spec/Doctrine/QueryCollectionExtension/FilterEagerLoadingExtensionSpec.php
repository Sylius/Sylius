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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Product;

final class FilterEagerLoadingExtensionSpec extends ObjectBehavior
{
    function let(ContextAwareQueryCollectionExtensionInterface $decoratedExtension): void
    {
        $this->beConstructedWith($decoratedExtension);
    }

    function it_does_nothing_if_current_resource_and_operation_forces_lazy_loading(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, Product::class, 'shop_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldNotBeCalled();
        $this->applyToCollection(...$args);
    }

    public function it_calls_filter_eager_loading_extension(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ContextAwareQueryCollectionExtensionInterface $decoratedExtension
    ): void {
        $args = [$queryBuilder, $queryNameGenerator, Order::class, 'shop_get', []];

        $decoratedExtension->applyToCollection(...$args)->shouldBeCalled();
        $this->applyToCollection(...$args);
    }
}
