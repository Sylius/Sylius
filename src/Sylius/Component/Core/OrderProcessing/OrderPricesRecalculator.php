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
            @trigger_error(
                sprintf('Passing a "Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface" to "%s" constructor is deprecated since Sylius 1.11 and will be prohibited in 2.0. Use "Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface" instead.', self::class),
                \E_USER_DEPRECATED,
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
