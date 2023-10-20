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

namespace Sylius\Bundle\ApiBundle\Tests\ApiPlatform\JsonLd\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\ResourceClassResolverInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\JsonLd\ContextBuilderInterface;
use ApiPlatform\JsonLd\Serializer\ItemNormalizer;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Metadata\Property\PropertyNameCollection;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Addressing\Model\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Copied and adjusted from API Platform
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ItemNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function testNormalize(): void
    {
        $country = new Country();
        $country->setCode('CODE');

        $resourceMetadataCollectionFactoryProphecy = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);
        $resourceMetadataCollectionFactoryProphecy->create(Country::class)->willReturn(new ResourceMetadataCollection('Country', [
            (new ApiResource())
                ->withShortName('Country')
                ->withOperations(new Operations(['get' => (new Get())->withShortName('Country')])),
        ]));
        $propertyNameCollection = new PropertyNameCollection(['code']);
        $propertyNameCollectionFactoryProphecy = $this->prophesize(PropertyNameCollectionFactoryInterface::class);
        $propertyNameCollectionFactoryProphecy->create(Country::class, Argument::any())->willReturn($propertyNameCollection);

        $propertyMetadata = (new ApiProperty())->withReadable(true);
        $propertyMetadataFactoryProphecy = $this->prophesize(PropertyMetadataFactoryInterface::class);
        $propertyMetadataFactoryProphecy->create(Country::class, 'code', Argument::any())->willReturn($propertyMetadata);

        $iriConverterProphecy = $this->prophesize(IriConverterInterface::class);
        $iriConverterProphecy->getIriFromResource($country, UrlGeneratorInterface::ABS_PATH, null, Argument::any())->willReturn('/countries/CODE');

        $resourceClassResolverProphecy = $this->prophesize(ResourceClassResolverInterface::class);
        $resourceClassResolverProphecy->getResourceClass($country, null)->willReturn(Country::class);
        $resourceClassResolverProphecy->getResourceClass(null, Country::class)->willReturn(Country::class);
        $resourceClassResolverProphecy->getResourceClass($country, Country::class)->willReturn(Country::class);
        $resourceClassResolverProphecy->getResourceClass(null, Country::class)->willReturn(Country::class);
        $resourceClassResolverProphecy->isResourceClass(Country::class)->willReturn(true);

        $serializerProphecy = $this->prophesize(SerializerInterface::class);
        $serializerProphecy->willImplement(NormalizerInterface::class);
        $serializerProphecy->normalize('CODE', null, Argument::type('array'))->willReturn('CODE');
        $contextBuilderProphecy = $this->prophesize(ContextBuilderInterface::class);
        $contextBuilderProphecy->getResourceContextUri(Country::class)->willReturn('/contexts/Country');

        $normalizer = new ItemNormalizer(
            $resourceMetadataCollectionFactoryProphecy->reveal(),
            $propertyNameCollectionFactoryProphecy->reveal(),
            $propertyMetadataFactoryProphecy->reveal(),
            $iriConverterProphecy->reveal(),
            $resourceClassResolverProphecy->reveal(),
            $contextBuilderProphecy->reveal(),
            null,
            null,
            null,
            []
        );
        $normalizer->setSerializer($serializerProphecy->reveal());

        $expected = [
            '@context' => '/contexts/Country',
            '@id' => '/countries/CODE',
            '@type' => 'Country',
            'code' => 'CODE',
        ];
        $this->assertEquals($expected, $normalizer->normalize($country));
    }
}
