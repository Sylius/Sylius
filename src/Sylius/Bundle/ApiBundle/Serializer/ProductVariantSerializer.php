<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductVariantSerializer implements ContextAwareNormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var ProductVariantPricesCalculatorInterface */
    private $priceCalculator;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        ProductVariantPricesCalculatorInterface $priceCalculator,
        ChannelContextInterface $channelContext,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->priceCalculator = $priceCalculator;
        $this->channelContext = $channelContext;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);

        $data = $this->objectNormalizer->normalize($object, $format, $context);
        
        try {
            $data['price'] = $this->priceCalculator->calculate($object, ['channel' => $this->channelContext->getChannel()]);
            $data['inStock'] = $this->availabilityChecker->isStockAvailable($object);
        } catch (ChannelNotFoundException $exception) {
            unset($data['price']);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        return $data instanceof ProductVariantInterface && $this->isNotAdminGetOperation($context);
    }

    private function isNotAdminGetOperation(array $context): bool
    {
        return !isset($context['item_operation_name']) || !($context['item_operation_name'] === 'admin_get');
    }
}
