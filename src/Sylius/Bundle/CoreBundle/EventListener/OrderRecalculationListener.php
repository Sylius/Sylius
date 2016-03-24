<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderRecalculationListener
{
    /**
     * @var OrderRecalculatorInterface
     */
    private $orderRecalculator;

    /**
     * @param OrderRecalculatorInterface $orderRecalculator
     */
    public function __construct(OrderRecalculatorInterface $orderRecalculator)
    {
        $this->orderRecalculator = $orderRecalculator;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function recalculateOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, OrderInterface::class);
        }

        $this->orderRecalculator->recalculate($order);
    }
}
