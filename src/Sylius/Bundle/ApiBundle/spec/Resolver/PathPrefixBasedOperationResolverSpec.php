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

namespace spec\Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Component\Addressing\Model\Country;

final class PathPrefixBasedOperationResolverSpec extends ObjectBehavior
{
    function let(
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        PathPrefixProviderInterface $pathPrefixProvider,
    ): void {
        $this->beConstructedWith($resourceMetadataCollectionFactory, $pathPrefixProvider);
    }

    function it_implements_the_operation_resolver_interface(): void
    {
        $this->shouldImplement(OperationResolverInterface::class);
    }

    function it_returns_given_operation_if_it_has_no_name(Operation $operation): void
    {
        $operation->getName()->willReturn(null);

        $this
            ->resolve(Country::class, 'api/v2/shop/countries/CODE', $operation)
            ->shouldReturn($operation)
        ;
    }
}
