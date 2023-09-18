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

use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantPriceCalculator implements ProductVariantPricesCalculatorInterface
{
    public function __construct(
        private ?ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker = null,
    ) {
        if ($this->productVariantLowestPriceDisplayChecker === null) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf('Not passing a $productVariantLowestPriceDisplayChecker to %s constructor is deprecated since Sylius 1.13 and will be prohibited in Sylius 2.0.', self::class),
            );
        }
    }

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

    public function calculateLowestPriceBeforeDiscount(ProductVariantInterface $productVariant, array $context): ?int
    {
        Assert::keyExists($context, 'channel');
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class);

        /** @var ChannelPricingInterface|null $channelPricing */
        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        if (null === $channelPricing) {
            throw MissingChannelConfigurationException::createForProductVariantChannelPricing($productVariant, $channel);
        }

        if (
            $this->productVariantLowestPriceDisplayChecker === null ||
            !$this->productVariantLowestPriceDisplayChecker->isLowestPriceDisplayable($productVariant, $context)
        ) {
            return null;
        }

        return $channelPricing->getLowestPriceBeforeDiscount();
    }
}
