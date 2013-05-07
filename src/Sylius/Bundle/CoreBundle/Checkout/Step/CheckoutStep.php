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

use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;

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
        return is_object($this->get('security.context')->getToken()->getUser());
    }
}
