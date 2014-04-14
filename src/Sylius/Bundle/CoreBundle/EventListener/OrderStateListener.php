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

use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order inventory processing listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderStateListener
{
    /**
     * States resolver.
     *
     * @var StateResolverInterface
     */
    protected $stateResolver;

    /**
     * Constructor.
     *
     * @param StateResolverInterface $stateResolver
     */
    public function __construct(StateResolverInterface $stateResolver)
    {
        $this->stateResolver = $stateResolver;
    }

    /**
     * Get the order from event and run the inventory processor on it.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function resolveOrderStates(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->stateResolver->resolvePaymentState($order);
        $this->stateResolver->resolveShippingState($order);
    }
}
