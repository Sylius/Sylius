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

use Sylius\Bundle\CartBundle\Resolver\ItemResolvingException;
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
class CartItemController extends Controller
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

        try {
            $item = $this->getResolver()->resolve($emptyItem, $request);
        } catch (ItemResolvingException $exception) {
            $errorMessage = $exception->getMessage() ?: 'Error occurred while adding item to cart';
            $this->setFlash('error', $errorMessage);

            return $this->redirectToCartSummary();
        }

        $cartOperator = $this->getOperator();

        $cartOperator
            ->addItem($cart, $item)
            ->refresh($cart)
        ;

        $errors = $this->get('validator')->validate($cart);

        if (0 === count($errors)) {
            $this->setFlash('success', 'Item has been added to cart');
            $cartOperator->save($cart);
        }

        return $this->redirectToCartSummary();
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
            $this->setFlash('error', 'Error occurred while removing item from cart');

            return $this->redirectToCartSummary();
        }

        $this
            ->getOperator()
            ->removeItem($cart, $item)
            ->refresh($cart)
            ->save($cart)
        ;

        $this->setFlash('success', 'Item has been removed from cart');

        return $this->redirectToCartSummary();
    }
}
