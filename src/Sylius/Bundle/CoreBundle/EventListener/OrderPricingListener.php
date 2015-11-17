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

use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;

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
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * Constructor.
     *
     * @param DelegatingCalculatorInterface $priceCalculator
     */
    public function __construct(
        DelegatingCalculatorInterface $priceCalculator,
        CartProviderInterface $cartProvider
    )
    {
        $this->priceCalculator = $priceCalculator;
        $this->cartProvider = $cartProvider;
    }

    /**
     * Recalculate the order unit prices.
     *
     * @param Event $event
     *
     * @throws UnexpectedTypeException
     */
    public function recalculatePrices(Event $event)
    {
        $order = $this->cartProvider->getCart();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $context = array();
        if (null !== $customer = $order->getCustomer()) {
            $context['customer'] = $customer;
            $context['groups'] = $customer->getGroups()->toArray();
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
