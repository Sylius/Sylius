<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductVariantSerializer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var ProductVariantPriceCalculator */
    private $priceCalculator;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        ProductVariantPriceCalculator $priceCalculator,
        ChannelContextInterface $channelContext
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->priceCalculator = $priceCalculator;
        $this->channelContext = $channelContext;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);

        $data = $this->objectNormalizer->normalize($object, $format, $context);
        $data['price'] = $this->priceCalculator->calculate($object, ['channel' => $this->channelContext->getChannel()]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ProductVariantInterface;
    }
}
