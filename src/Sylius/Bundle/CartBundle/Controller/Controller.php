<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Controller;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Base controller for cart system controllers.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Controller extends ResourceController
{
    /**
     * Redirect to cart summary page.
     *
     * @return RedirectResponse
     */
    protected function redirectToCartSummary()
    {
        return $this->redirect($this->generateUrl($this->getCartSummaryRoute()));
    }

    /**
     * Cart summary page route.
     *
     * @return string
     */
    protected function getCartSummaryRoute()
    {
        return 'sylius_cart_summary';
    }

    /**
     * Get current cart using the provider service.
     *
     * @return CartInterface
     */
    protected function getCurrentCart()
    {
        return $this
            ->getProvider()
            ->getCart()
        ;
    }

    /**
     * Get cart provider.
     *
     * @return CartProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('sylius.cart_provider');
    }

    /**
     * Get cart item resolver.
     * This service is used to build the new cart item instance.
     *
     * @return ItemResolverInterface
     */
    protected function getResolver()
    {
        return $this->container->get('sylius.cart_resolver');
    }
}
