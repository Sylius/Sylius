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

namespace Sylius\Component\Core\Calculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantPriceCalculator implements ProductVariantPriceCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ProductVariantInterface $productVariant, array $context): int
    {
        Assert::keyExists($context, 'channel');

        $channelPricing = $productVariant->getChannelPricingForChannel($context['channel']);

        if (null === $channelPricing) {
            $message = sprintf('Channel %s has no price defined for product variant', $context['channel']->getName());

            if ($productVariant->getName() !== null) {
                $message .= sprintf(' %s (%s)', $productVariant->getName(), $productVariant->getCode());
            } else {
                $message .= sprintf(' with code %s', $productVariant->getCode());
            }

            throw new MissingChannelConfigurationException($message);
        }

        return $channelPricing->getPrice();
    }
}
