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

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class OrderPricesRecalculator implements OrderProcessorInterface
{
    private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator;

    public function __construct(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $channel = $order->getChannel();

        foreach ($order->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $item->setUnitPrice($this->productVariantPriceCalculator->calculate(
                $item->getVariant(),
                ['channel' => $channel]
            ));
        }
    }
}
