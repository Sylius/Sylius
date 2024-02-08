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

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantPriceCalculator implements ProductVariantPricesCalculatorInterface
{
    public function calculate(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing || $channelPricing->getPrice() === null) {
            throw MissingChannelConfigurationException::createForProductVariantChannelPricing($productVariant, $context['channel']);
        }

        return $channelPricing->getPrice();
    }

    /**
     * @throws \InvalidArgumentException|MissingChannelConfigurationException
     */
    public function calculateOriginal(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing) {
            throw MissingChannelConfigurationException::createForProductVariantChannelPricing($productVariant, $context['channel']);
        }

        if (null !== $channelPricing->getOriginalPrice()) {
            return $channelPricing->getOriginalPrice();
        }

        if ($channelPricing->getPrice() !== null) {
            return $channelPricing->getPrice();
        }

        throw MissingChannelConfigurationException::createForProductVariantChannelPricing($productVariant, $context['channel']);
    }
}
