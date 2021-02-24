<?php

declare(strict_types=1);


namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculator;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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

    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $object;

        $data = $this->objectNormalizer->normalize($object, $format, $context);
        $data['price'] = $this->priceCalculator->calculate($productVariant, ['channel' => $this->channelContext->getChannel()]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof ProductVariantInterface;
    }
}
