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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
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
     * @throws UnexpectedTypeException
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

        if (null !== $order->getChannel()) {
            $context['channel'] = array($order->getChannel());
        }

        foreach ($order->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $context['quantity'] = $item->getQuantity();
            $item->setUnitPrice($this->priceCalculator->calculate($item->getVariant(), $context));
        }

        $order->calculateTotal();
    }
}
