<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\EventListener;

use Sylius\Core\Model\OrderInterface;
use Sylius\Currency\Context\CurrencyContextInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets currently selected currency on order object.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderCurrencyListener
{
    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException when event's subject is not an order
     */
    public function processOrderCurrency(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                OrderInterface::class
            );
        }

        $order->setCurrencyCode($this->currencyContext->getCurrencyCode());
    }
}
