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

use Finite\Event\TransitionEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order inventory processing listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
     * Get the order from event and run the state resolver on it.
     *
     * @param GenericEvent $event
     */
    public function resolveOrderStates(GenericEvent $event)
    {
        $this->resolve($event->getSubject());
    }

    public function resolveOrderStatesFinite(TransitionEvent $event)
    {
        $order = $event->getStateMachine()->getObject();

        if ($order instanceof OrderInterface) {
            $this->resolve($order);
        }
    }

    protected function resolve(OrderInterface $order)
    {
        $this->stateResolver->resolvePaymentState($order);
        $this->stateResolver->resolveShippingState($order);
    }
}
