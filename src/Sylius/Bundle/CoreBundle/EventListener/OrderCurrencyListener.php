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
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Sets currently selected currency on order object.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderCurrencyListener
{
    protected $currencyContext;

    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * Sets currency on the order
     *
     * @throws UnexpectedTypeException when event's subject is not an order
     *
     * @param GenericEvent $event
     */
    public function processOrderCurrency(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $order->setCurrency($this->currencyContext->getCurrency());
    }
}
