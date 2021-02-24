<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class ProductVariantSerializer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var ProductVariantPriceCalculatorInterface */
    private $priceCalculator;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        ProductVariantPriceCalculatorInterface $priceCalculator,
        ChannelContextInterface $channelContext
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->priceCalculator = $priceCalculator;
        $this->channelContext = $channelContext;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);

        $data = $this->objectNormalizer->normalize($object, $format, $context);
        if (isset($data['product'])) {
            $data['product'] = $data['product']['@id'];
            $data['translations'] = $this->serializeTranslation($data['translations']);
        }
        $data['price'] = $this->priceCalculator->calculate($object, ['channel' => $this->channelContext->getChannel()]);

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if(
            $data instanceof ProductVariantInterface &&
            $context['operation_type'] !== 'subresource' &&
            array_key_exists('groups', $context) &&
            $context['groups'] != 'shop:cart:read' &&
            $context['groups'] != 'admin:order_item_unit:read' &&
            $context['groups'] != 'admin:product:read'
        ) {
            return true;
        }
        return false;
    }

    private function serializeTranslation($translations): array
    {
        $serializedTranslations = [];

        foreach ($translations['hydra:member'] as $translation) {
            $serializedTranslations[$translation['locale']] = $translation;
        }

        return $serializedTranslations;
    }
}
