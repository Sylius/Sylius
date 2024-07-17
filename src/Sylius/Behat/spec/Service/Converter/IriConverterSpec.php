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

namespace spec\Sylius\Behat\Service\Converter;

use ApiPlatform\Api\IriConverterInterface as BaseIriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Service\Converter\IriConverterInterface;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixes;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;

final class IriConverterSpec extends ObjectBehavior
{
    function let(
        BaseIriConverterInterface $decoratedIriConverter,
        OperationResolverInterface $operationResolver,
    ): void {
        $this->beConstructedWith($decoratedIriConverter, $operationResolver);
    }

    function it_implements_the_behat_iri_converter_interface(): void
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

    function it_uses_inner_iri_converter_to_get_iri_from_resource(
        IriConverterInterface $decoratedIriConverter,
        CountryInterface $country,
    ): void {
        $decoratedIriConverter
            ->getIriFromResource($country->getWrappedObject(), UrlGeneratorInterface::ABS_PATH, null, [])
            ->willReturn('api/v2/admin/countries/CODE')
        ;

        $this->getIriFromResource($country->getWrappedObject())->shouldReturn('api/v2/admin/countries/CODE');
    }

    function it_provides_iri_from_resource_in_given_section(
        IriConverterInterface $decoratedIriConverter,
        OperationResolverInterface $operationResolver,
        CountryInterface $country,
        Operation $operation,
    ): void {
        $operationResolver
            ->resolve(Country::class, PathPrefixes::ADMIN_PREFIX, null)
            ->willReturn($operation)
        ;

        $decoratedIriConverter
            ->getIriFromResource(
                $country->getWrappedObject(),
                UrlGeneratorInterface::ABS_PATH,
                $operation,
                [
                    'force_resource_class' => Country::class,
                ],
            )
            ->willReturn('api/v2/admin/countries/CODE')
        ;

        $this
            ->getIriFromResourceInSection(
                $country->getWrappedObject(),
                PathPrefixes::ADMIN_PREFIX,
                UrlGeneratorInterface::ABS_PATH,
                null,
                [
                    'force_resource_class' => Country::class,
                ],
            )
            ->shouldReturn('api/v2/admin/countries/CODE')
        ;
    }
}
