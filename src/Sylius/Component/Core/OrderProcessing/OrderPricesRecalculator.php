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

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class OrderPricesRecalculator implements OrderProcessorInterface
{
    public function __construct(private ProductVariantPriceCalculatorInterface|ProductVariantPricesCalculatorInterface $productVariantPriceCalculator)
    {
        if ($this->productVariantPriceCalculator instanceof ProductVariantPriceCalculatorInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.11',
                'Passing a "%s" to "%s" constructor is deprecated and will be prohibited in 2.0. Use "%s" instead.',
                ProductVariantPriceCalculatorInterface::class,
                self::class,
                ProductVariantPricesCalculatorInterface::class,
            );
        }
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (!$order->canBeProcessed()) {
            return;
        }

        $channel = $order->getChannel();

        foreach ($order->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $item->setUnitPrice($this->productVariantPriceCalculator->calculate(
                $item->getVariant(),
                ['channel' => $channel],
            ));

            if ($this->productVariantPriceCalculator instanceof ProductVariantPricesCalculatorInterface) {
                $item->setOriginalUnitPrice($this->productVariantPriceCalculator->calculateOriginal(
                    $item->getVariant(),
                    ['channel' => $channel],
                ));
            }
        }
    }
}
