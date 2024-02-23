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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Bundle\ApiBundle\Exception\ChannelPricingChannelCodeMismatchException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class ProductVariantChannelPricingsChannelCodeKeyDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_variant_channel_pricings_channel_code_key_denormalizer_already_called';

    private const KEY_CHANNEL_PRICINGS = 'channelPricings';

    private const KEY_CHANNEL_CODE = 'channelCode';

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            is_array($data) &&
            is_a($type, ProductVariantInterface::class, true)
        ;
    }

    /** @param array<string, array{ channelPricings: array<array-key, mixed> }> $data */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        if (array_key_exists(self::KEY_CHANNEL_PRICINGS, $data)) {
            foreach ($data[self::KEY_CHANNEL_PRICINGS] as $key => &$channelPricing) {
                if (array_key_exists(self::KEY_CHANNEL_CODE, $channelPricing) && $channelPricing[self::KEY_CHANNEL_CODE] !== $key) {
                    throw new ChannelPricingChannelCodeMismatchException(sprintf(
                        'The channelCode of channelPricing does not match the key. Key: "%s", channelCode: "%s"',
                        $key,
                        $channelPricing[self::KEY_CHANNEL_CODE],
                    ));
                }

                $channelPricing[self::KEY_CHANNEL_CODE] = $key;
            }
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }
}
