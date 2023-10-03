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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Calendar\Tests\Behat\Context\Setup\CalendarContext;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Webmozart\Assert\Assert;

final class PriceHistoryContext implements Context
{
    public function __construct(
        private CalendarContext $calendarContext,
        private ObjectManager $channelPricingManager,
        private ProductVariantResolverInterface $defaultVariantResolver,
    ) {
    }

    /**
     * @Given /^on "([^"]+)" (its) price changed to ("[^"]+")$/
     */
    public function onDayItsPriceChangedTo(string $date, ProductInterface $product, int $price): void
    {
        $this->calendarContext->itIsNow($date);

        $channelPricing = $this->getChannelPricingFromProduct($product);

        $channelPricing->setPrice($price);

        $this->channelPricingManager->flush();
    }

    /**
     * @Given /^on "([^"]+)" (its) original price changed to ("[^"]+")$/
     */
    public function onDayItsOriginalPriceChangedTo(string $date, ProductInterface $product, int $originalPrice): void
    {
        $this->calendarContext->itIsNow($date);

        $channelPricing = $this->getChannelPricingFromProduct($product);

        $channelPricing->setOriginalPrice($originalPrice);

        $this->channelPricingManager->flush();
    }

    /**
     * @Given /^on "([^"]+)" (its) price changed to ("[^"]+") and original price to ("[^"]+")$/
     */
    public function onDayItsOriginalPriceChangedToAndOriginalPriceTo(string $date, ProductInterface $product, int $price, int $originalPrice): void
    {
        $this->calendarContext->itIsNow($date);

        $channelPricing = $this->getChannelPricingFromProduct($product);

        $channelPricing->setPrice($price);
        $channelPricing->setOriginalPrice($originalPrice);

        $this->channelPricingManager->flush();
    }

    /**
     * @Given /^on "([^"]+)" (its) original price has been removed$/
     */
    public function onDayItsOriginalPriceHasBeenRemoved(string $date, ProductInterface $product): void
    {
        $this->calendarContext->itIsNow($date);

        $channelPricing = $this->getChannelPricingFromProduct($product);

        $channelPricing->setOriginalPrice(null);

        $this->channelPricingManager->flush();
    }

    private function getChannelPricingFromProduct(ProductInterface $product): ChannelPricingInterface
    {
        $variant = $this->defaultVariantResolver->getVariant($product);
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $channelPricing = $variant->getChannelPricings()->first();
        Assert::isInstanceOf($channelPricing, ChannelPricingInterface::class);

        return $channelPricing;
    }
}
