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

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PricingBundle\Calculator\DelegatingCalculatorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order pricing listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderPricingListener
{
    /**
     * @var DelegatingCalculatorInterface
     */
    private $priceCalculator;

    /**
     * Constructor.
     *
     * @param DelegatingCalculatorInterface $priceCalculator
     */
    public function __construct(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Recalculate the order unit prices.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function recalculatePrices(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order promotion listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $context = array();

        if (null !== $user = $order->getUser()) {
            $context['user']   = $user;
            $context['groups'] = $user->getGroups();
        }

        foreach ($order->getItems() as $item) {
            $priceable = $item->getVariant();

            $context['quantity'] = $item->getQuantity();
            $item->setUnitPrice($this->priceCalculator->calculate($priceable, $context));
        }

        $order->calculateTotal();
    }
}
