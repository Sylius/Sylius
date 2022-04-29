<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductVariantNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'product_variant_normalizer_already_called';

    private ProductVariantPricesCalculatorInterface $priceCalculator;

    private ChannelContextInterface $channelContext;

    private AvailabilityCheckerInterface $availabilityChecker;

    public function __construct(
        ProductVariantPricesCalculatorInterface $priceCalculator,
        ChannelContextInterface $channelContext,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->channelContext = $channelContext;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        try {
            $data['price'] = $this->priceCalculator->calculate($object, ['channel' => $this->channelContext->getChannel()]);
        } catch (ChannelNotFoundException $exception) {
            unset($data['price']);
        }

        $data['inStock'] = $this->availabilityChecker->isStockAvailable($object);

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductVariantInterface && $this->isNotAdminGetOperation($context);
    }

    private function isNotAdminGetOperation(array $context): bool
    {
        return !isset($context['item_operation_name']) || !($context['item_operation_name'] === 'admin_get');
    }
}
