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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Base class for checkout steps.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class CheckoutStep extends ControllerStep
{
    /**
     * Get cart provider.
     *
     * @return CartProviderInterface
     */
    protected function getCartProvider()
    {
        return $this->get('sylius.cart_provider');
    }

    /**
     * Get current cart instance.
     *
     * @return CartInterface
     */
    protected function getCurrentCart()
    {
        return $this->getCartProvider()->getCart();
    }

    /**
     * Get object manager.
     *
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->get('doctrine')->getManager();
    }

    /**
     * Get zone matcher.
     *
     * @return ZoneMatcherInterface
     */
    protected function getZoneMatcher()
    {
        return $this->get('sylius.zone_matcher');
    }

    /**
     * Is user logged in?
     *
     * @return Boolean
     */
    protected function isUserLoggedIn()
    {
        try {
            return $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * Dispatch event.
     *
     * @param string $name
     * @param Event  $event
     */
    protected function dispatchEvent($name, Event $event)
    {
        $this->get('event_dispatcher')->dispatch($name, $event);
    }

    /**
     * Dispatch checkout event.
     *
     * @param string         $name
     * @param OrderInterface $order
     */
    protected function dispatchCheckoutEvent($name, OrderInterface $order)
    {
        $this->dispatchEvent($name, new GenericEvent($order));
    }
}
