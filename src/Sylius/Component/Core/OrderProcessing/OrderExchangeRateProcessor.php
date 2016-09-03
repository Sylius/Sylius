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

use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OrderExchangeRateProcessor implements OrderProcessorInterface
{
    /**
     * @var OrderUpdaterInterface
     */
    private $exchangeRateUpdater;

    /**
     * @param OrderUpdaterInterface $exchangeRateUpdater
     */
    public function __construct(OrderUpdaterInterface $exchangeRateUpdater)
    {
        $this->exchangeRateUpdater = $exchangeRateUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return;
        }

        $this->exchangeRateUpdater->update($order);
    }
}
