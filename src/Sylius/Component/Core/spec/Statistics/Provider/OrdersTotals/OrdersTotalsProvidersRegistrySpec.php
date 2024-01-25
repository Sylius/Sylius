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

namespace spec\Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProvidersRegistry;

final class OrdersTotalsProvidersRegistrySpec extends ObjectBehavior
{
    function let(
        OrdersTotalsProviderInterface $first,
        OrdersTotalsProviderInterface $second,
    ): void {
        $this->beConstructedWith(new \ArrayIterator([
            'first' => $first->getWrappedObject(),
            'second' => $second->getWrappedObject(),
        ]));
    }

    function it_is_a_orders_totals_providers_registry(): void
    {
        $this->shouldImplement(OrdersTotalsProviderRegistryInterface::class);
    }

    function it_throws_exception_when_provider_with_given_type_does_not_exist(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getByType', ['dummy'])
        ;
    }

    function it_returns_registered_provider_by_type(OrdersTotalsProviderInterface $second): void
    {
        $this->getByType('second')->shouldReturn($second);
    }
}
