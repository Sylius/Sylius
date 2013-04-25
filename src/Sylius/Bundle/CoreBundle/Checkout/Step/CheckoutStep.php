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

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
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
     * Is user logged in?
     *
     * @return Boolean
     */
    protected function isUserLoggedIn()
    {
        return is_object($this->get('security.context')->getToken()->getUser());
    }

    /**
     * Save address with given id.
     *
     * @param AddressInterface
     */
    protected function saveAddress(AddressInterface $address)
    {
        $addressManager = $this->get('sylius.manager.address');

        $addressManager->persist($address);
        $addressManager->flush($address);
    }

    /**
     * Get address with given id.
     *
     * @return AddressInterface
     */
    protected function getAddress($id)
    {
        $addressRepository = $this->get('sylius.repository.address');

        return $addressRepository->find($id);
    }
}
