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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\InvalidProductAttributeValueTypeException;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductAttributeValueDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_product_attribute_value_denormalizer_already_called';

    function let(IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($iriConverter);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], ProductAttributeValueInterface::class, context: [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', ProductAttributeValueInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_product_attribute_value(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_throws_an_exception_if_given_value_is_in_wrong_type(
        IriConverterInterface $iriConverter,
        DenormalizerInterface $denormalizer,
        ProductAttributeInterface $attribute,
    ): void {
        $iriConverter->getResourceFromIri('/attributes/material')->willReturn($attribute);

        $attribute->getStorageType()->willReturn('text');
        $attribute->getName()->willReturn('Material');

        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize([], ProductAttributeValueInterface::class, null, [self::ALREADY_CALLED => true])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(InvalidProductAttributeValueTypeException::class)
            ->during('denormalize', [
                ['attribute' => '/attributes/material', 'value' => 4],
                ProductAttributeValueInterface::class,
            ])
        ;
    }

    function it_denormalizes_data_if_given_value_is_in_proper_types(
        IriConverterInterface $iriConverter,
        DenormalizerInterface $denormalizer,
        ProductAttributeInterface $attribute,
    ): void {
        $iriConverter->getResourceFromIri('/attributes/material')->willReturn($attribute);

        $attribute->getStorageType()->willReturn('text');
        $attribute->getType()->willReturn('text');

        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize(
                ['attribute' => '/attributes/material', 'value' => 'ceramic'],
                ProductAttributeValueInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->shouldBeCalled()
        ;

        $this->denormalize(
            ['attribute' => '/attributes/material', 'value' => 'ceramic'],
            ProductAttributeValueInterface::class,
        );
    }
}
