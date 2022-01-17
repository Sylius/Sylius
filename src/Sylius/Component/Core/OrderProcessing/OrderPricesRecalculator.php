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
    public function __construct(private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (OrderInterface::STATE_CART !== $order->getState()) {
            return;
        }

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
