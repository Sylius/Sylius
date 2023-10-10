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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_product_denormalizer_already_called';

    function let(IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($iriConverter);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], ProductInterface::class, context: [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', ProductInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_product(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_throws_an_exception_if_given_attribute_value_is_in_wrong_type(
        IriConverterInterface $iriConverter,
        DenormalizerInterface $denormalizer,
        ProductAttributeInterface $colorAttribute,
        ProductAttributeInterface $materialAttribute,
    ): void {
        $iriConverter->getResourceFromIri('/attributes/color')->willReturn($colorAttribute);
        $iriConverter->getResourceFromIri('/attributes/material')->willReturn($materialAttribute);

        $colorAttribute->getStorageType()->willReturn('text');
        $colorAttribute->getName()->willReturn('Color');

        $materialAttribute->getStorageType()->willReturn('text');
        $materialAttribute->getName()->willReturn('Material');

        $this->setDenormalizer($denormalizer);
        $denormalizer->denormalize([], ProductInterface::class, null, [self::ALREADY_CALLED => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(InvalidProductAttributeValueTypeException::class)
            ->during('denormalize', [[
                'attributes' => [
                    ['attribute' => '/attributes/color', 'value' => 'green'],
                    ['attribute' => '/attributes/material', 'value' => 4],
                ]],
                ProductInterface::class,
            ])
        ;
    }

    function it_denormalizes_data_if_given_attribute_values_are_in_proper_types(
        IriConverterInterface $iriConverter,
        DenormalizerInterface $denormalizer,
        ProductAttributeInterface $colorAttribute,
        ProductAttributeInterface $materialAttribute,
    ): void {
        $iriConverter->getResourceFromIri('/attributes/color')->willReturn($colorAttribute);
        $iriConverter->getResourceFromIri('/attributes/material')->willReturn($materialAttribute);

        $colorAttribute->getStorageType()->willReturn('text');
        $colorAttribute->getType()->willReturn('text');

        $materialAttribute->getStorageType()->willReturn('text');
        $materialAttribute->getType()->willReturn('text');

        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize([
                'attributes' => [
                    ['attribute' => '/attributes/color', 'value' => 'green'],
                    ['attribute' => '/attributes/material', 'value' => 'ceramic'],
                ]],
                ProductInterface::class,
                null,
                [self::ALREADY_CALLED => true]
            )
            ->shouldBeCalled()
        ;

        $this->denormalize([
            'attributes' => [
                ['attribute' => '/attributes/color', 'value' => 'green'],
                ['attribute' => '/attributes/material', 'value' => 'ceramic'],
            ]],
            ProductInterface::class,
        );
    }
}
