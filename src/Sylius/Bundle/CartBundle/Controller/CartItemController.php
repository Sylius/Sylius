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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cart item controller.
 *
 * It manages the cart item resource, but also it has
 * two handy methods for easy adding and removing items
 * using the services, an operator and resolver.
 *
 * The operator performs basic cart operations,
 * adding, removing items, saving and clearing the cart.
 *
 * The resolver is used to create a new cart item, based
 * on the data from current request.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItemController extends ResourceController
{
    /**
     * Adds item to cart.
     * It uses the resolver service so you can populate the new item instance
     * with proper values based on current request.
     *
     * It redirect to cart summary page by default.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $cart = $this->getCurrentCart();
        $emptyItem = $this->createNew();

        $item = $this->getResolver()->resolve($emptyItem, $request);

        if (!$item) {
            $this->setFlash('error', 'sylius_cart.flashes.add.error');

            return $this->redirectToCart();
        }

        $cartOperator = $this->getOperator();

        $cartOperator
            ->addItem($cart, $item)
            ->refresh($cart)
        ;

        $errors = $this->get('validator')->validate($cart);

        if (0 === count($errors)) {
            $this->setFlash('success', 'sylius_cart.flashes.add.success');
            $cartOperator->save($cart);
        }

        return $this->redirectToCart();
    }

    /**
     * Removes item from cart.
     * It takes an item id as an argument.
     *
     * If the item is found and the current user cart contains that item,
     * it will be removed and the cart - refreshed and saved.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function removeAction($id)
    {
        $cart = $this->getCurrentCart();
        $item = $this->getRepository()->find($id);

        if (!$item || false === $cart->hasItem($item)) {
            $this->setFlash('error', 'sylius_cart.flashes.remove.error');

            return $this->redirectToCart();
        }

        $this
            ->getOperator()
            ->removeItem($cart, $item)
            ->refresh($cart)
            ->save($cart)
        ;

        $this->setFlash('success', 'sylius_cart.flashes.remove.success');

        return $this->redirectToCart();
    }

    /**
     * Redirect to cart summary page.
     *
     * @return RedirectResponse
     */
    protected function redirectToCart()
    {
        return $this->redirect($this->generateUrl($this->getCartRoute()));
    }

    /**
     * Cart summary page route.
     *
     * @return string
     */
    protected function getCartRoute()
    {
        return 'sylius_cart_show';
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
        return $this->get('sylius_cart.provider');
    }

    /**
     * Get cart operator.
     *
     * @return CartOperatorInterface
     */
    protected function getOperator()
    {
        return $this->get('sylius_cart.operator');
    }

    /**
     * Get cart item resolver.
     * This service is used to build the new cart item instance.
     *
     * @return CartResolverInterface
     */
    protected function getResolver()
    {
        return $this->get('sylius_cart.resolver');
    }
}
