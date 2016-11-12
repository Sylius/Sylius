<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPricesRecalculator implements OrderProcessorInterface
{
    /**
     * @var DelegatingCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @param DelegatingCalculatorInterface $priceCalculator
     */
    public function __construct(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order)
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $context = [];
        if (null !== $customer = $order->getCustomer()) {
            $context['customer'] = $customer;
            $context['groups'] = [$customer->getGroup()];
        }

        if (null !== $order->getChannel()) {
            $context['channel'] = $order->getChannel();
        }

        if (null !== $order->getCurrencyCode()) {
            $context['currency'] = $order->getCurrencyCode();
        }

        foreach ($order->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $context['quantity'] = $item->getQuantity();
            $item->setUnitPrice($this->priceCalculator->calculate($item->getVariant(), $context));
        }
    }
}
