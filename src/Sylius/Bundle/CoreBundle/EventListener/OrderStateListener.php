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

use SM\Event\TransitionEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Sylius\Component\Order\Model\OrderAwareInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderStateListener
{
    /**
     * @var StateResolverInterface
     */
    protected $stateResolver;

    /**
     * @param StateResolverInterface $stateResolver
     */
    public function __construct(StateResolverInterface $stateResolver)
    {
        $this->stateResolver = $stateResolver;
    }

    /**
     * @param GenericEvent $event
     */
    public function resolveOrderStates(GenericEvent $event)
    {
        $this->resolve($event->getSubject());
    }

    /**
     * @param TransitionEvent $event
     */
    public function resolveOrderStatesOnTransition(TransitionEvent $event)
    {
        $resource = $event->getStateMachine()->getObject();

        if ($resource instanceof OrderInterface) {
            $this->resolve($resource);
        } elseif ($resource instanceof OrderAwareInterface && null !== $resource->getOrder()) {
            $this->resolve($resource->getOrder());
        }
    }

    /**
     * @param OrderInterface $order
     */
    protected function resolve(OrderInterface $order)
    {
        $this->stateResolver->resolvePaymentState($order);
        $this->stateResolver->resolveShippingState($order);
    }
}
