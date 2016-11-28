<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantPriceCalculator implements ProductVariantPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ProductVariantInterface $productVariant, array $context)
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);
        Assert::notNull($channelPricing);

        return $channelPricing->getPrice();
    }
}
