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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ChannelPricingChannelCodeMismatchException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductVariantChannelPricingsChannelCodeKeyDenormalizerSpec extends ObjectBehavior
{
    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], ProductVariantInterface::class, context: [
                'sylius_product_variant_channel_pricings_channel_code_key_denormalizer_already_called' => true,
            ])->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', ProductVariantInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_product_variant(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_does_nothing_if_there_is_no_channel_pricings_key(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $this->denormalize([], ProductVariantInterface::class);

        $denormalizer->denormalize([], ProductVariantInterface::class, null, [
            'sylius_product_variant_channel_pricings_channel_code_key_denormalizer_already_called' => true,
        ])->shouldHaveBeenCalledOnce();
    }

    function it_changes_keys_of_channel_pricings_to_channel_code(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $originalData = ['channelPricings' => ['WEB' => ['channelCode' => 'WEB'], 'MOBILE' => []]];
        $updatedData = ['channelPricings' => ['WEB' => ['channelCode' => 'WEB'], 'MOBILE' => ['channelCode' => 'MOBILE']]];

        $this->denormalize($originalData, ProductVariantInterface::class);

        $denormalizer->denormalize(
            $updatedData,
            ProductVariantInterface::class,
            null,
            ['sylius_product_variant_channel_pricings_channel_code_key_denormalizer_already_called' => true],
        )->shouldHaveBeenCalledOnce();
    }

    function it_throws_an_exception_if_channel_code_is_not_the_same_as_key(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $this
            ->shouldThrow(ChannelPricingChannelCodeMismatchException::class)
            ->during('denormalize', [['channelPricings' => ['WEB' => ['channelCode' => 'MOBILE']]], ProductVariantInterface::class])
        ;
    }
}
