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

namespace Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use ApiPlatform\Api\IriConverterInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

final class ChannelPriceHistoryConfigDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_channel_price_history_config_denormalizer_already_called';

    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly FactoryInterface $channelPriceHistoryConfigFactory,
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            is_array($data) &&
            is_a($type, ChannelPriceHistoryConfigInterface::class, true)
        ;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = (array) $data;

        $channelPriceHistoryConfig = $this->denormalizer->denormalize($data, $type, $format, $context);
        Assert::isInstanceOf($channelPriceHistoryConfig, ChannelPriceHistoryConfigInterface::class);
        $channelPriceHistoryConfig->clearTaxonsExcludedFromShowingLowestPrice();

        foreach ($data['taxonsExcludedFromShowingLowestPrice'] ?? [] as $excludedTaxonIri) {
            /** @var TaxonInterface $taxon */
            $taxon = $this->iriConverter->getResourceFromIri($excludedTaxonIri);

            $channelPriceHistoryConfig->addTaxonExcludedFromShowingLowestPrice($taxon);
        }

        return $channelPriceHistoryConfig;
    }
}
