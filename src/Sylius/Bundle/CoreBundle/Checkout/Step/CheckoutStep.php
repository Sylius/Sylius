<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FlowBundle\Process\Step\AbstractControllerStep;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Exception\InvalidTransitionException;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class CheckoutStep extends AbstractControllerStep
{
    /**
     * @return CartProviderInterface
     */
    protected function getCartProvider()
    {
        return $this->get('sylius.cart_provider');
    }

    /**
     * @return OrderInterface
     */
    protected function getCurrentCart()
    {
        return $this->getCartProvider()->getCart();
    }

    /**
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->get('doctrine')->getManager();
    }

    /**
     * @return ZoneMatcherInterface
     */
    protected function getZoneMatcher()
    {
        return $this->get('sylius.zone_matcher');
    }

    /**
     * @return bool
     */
    protected function isUserLoggedIn()
    {
        try {
            return $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param string $name
     * @param Event $event
     */
    protected function dispatchEvent($name, Event $event)
    {
        $this->get('event_dispatcher')->dispatch($name, $event);
    }

    /**
     * @param string $name
     * @param OrderInterface $order
     */
    protected function dispatchCheckoutEvent($name, OrderInterface $order)
    {
        $this->dispatchEvent($name, new GenericEvent($order));
    }

    /**
     * @param string $transition
     * @param OrderInterface $order
     * @param bool $flush
     *
     * @throws InvalidTransitionException
     */
    protected function applyTransition($transition, OrderInterface $order, $flush = true)
    {
        $stateMachineFactory = $this->get('sm.factory');
        $cartStateMachine = $stateMachineFactory->get($order, 'sylius_order_checkout');

        if (!$cartStateMachine->can($transition)) {
            throw new InvalidTransitionException($transition, $cartStateMachine);
        }

        $cartStateMachine->apply($transition);

        if ($flush) {
            $this->getManager()->flush($order);
        }
    }
}
