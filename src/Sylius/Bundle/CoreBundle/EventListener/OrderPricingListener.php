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
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
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
            throw new UnexpectedTypeException($order, 'Sylius\Component\Order\Model\OrderInterface');
        }

        $context = array();

        if (null !== $user = $order->getUser()) {
            $context['user']   = $user;
            $context['groups'] = $user->getGroups()->toArray();
        }

        foreach ($order->getItems() as $item) {
            $priceable = $item->getVariant();

            $context['quantity'] = $item->getQuantity();
            $item->setUnitPrice($this->priceCalculator->calculate($priceable, $context));
        }

        $order->calculateTotal();
    }
}
