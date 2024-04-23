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

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform\Routing;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;

final class IriConverterSpec extends ObjectBehavior
{
    function let(
        IriConverterInterface $decoratedIriConverter,
        PathPrefixProviderInterface $pathPrefixProvider,
        OperationResolverInterface $operationResolver,
    ): void {
        $this->beConstructedWith($decoratedIriConverter, $pathPrefixProvider, $operationResolver);
    }

    function it_implements_the_iri_converter_interface(): void
    {
        $this->shouldImplement(IriConverterInterface::class);
    }

    function it_uses_inner_iri_converter_to_get_resource_from_iri(
        IriConverterInterface $decoratedIriConverter,
        CountryInterface $country,
    ): void {
        $decoratedIriConverter->getResourceFromIri('api/v2/admin/countries/CODE', [], null)->willReturn($country);

        $this->getResourceFromIri('api/v2/admin/countries/CODE')->shouldReturn($country);
    }

    function it_uses_operation_resolver_to_get_proper_iri_from_resource(
        IriConverterInterface $decoratedIriConverter,
        PathPrefixProviderInterface $pathPrefixProvider,
        OperationResolverInterface $operationResolver,
        CountryInterface $country,
        Operation $operation,
    ): void {
        $pathPrefixProvider->getPathPrefix('api/v2/admin/countries')->willReturn('admin');

        $operationResolver
            ->resolve(Country::class, 'admin', null)
            ->willReturn($operation)
        ;

        $decoratedIriConverter
            ->getIriFromResource(
                $country->getWrappedObject(),
                UrlGeneratorInterface::ABS_PATH,
                $operation,
                [
                    'request_uri' => 'api/v2/admin/countries',
                    'force_resource_class' => Country::class,
                ],
            )
            ->willReturn('api/v2/admin/countries/CODE')
        ;

        $this
            ->getIriFromResource(
                $country->getWrappedObject(),
                UrlGeneratorInterface::ABS_PATH,
                null,
                [
                    'request_uri' => 'api/v2/admin/countries',
                    'force_resource_class' => Country::class,
                ],
            )
            ->shouldReturn('api/v2/admin/countries/CODE')
        ;
    }
}
