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

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Bundle\CartBundle\Event\CartItemEvent;
use Sylius\Bundle\CartBundle\Event\FlashEvent;
use Sylius\Bundle\CartBundle\Resolver\ItemResolvingException;

/**
 * Cart item controller.
 *
 * It manages the cart item resource, but also it has
 * two handy methods for easy adding and removing items
 * using the services, an operator and resolver.
 *
 * The basic cart operations like: adding, removing items,
 * saving and clearing the cart are done in listeners.
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
            // Write flash message
            $this->dispatchEvent(SyliusCartEvents::ITEM_ADD_ERROR, new FlashEvent($exception->getMessage()));

            return $this->redirectAfterAdd($request);
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);
        $event->isValid(false);

        // Update models
        $this->dispatchEvent(SyliusCartEvents::ITEM_ADD_INITIALIZE, $event);
        $this->dispatchEvent(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $this->dispatchEvent(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $this->dispatchEvent(SyliusCartEvents::ITEM_ADD_COMPLETED, new FlashEvent());

        return $this->redirectAfterAdd($request);
    }

    /**
     * Redirect to specific URL or to cart.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function redirectAfterAdd(Request $request)
    {
        if ($request->query->has('_redirect_to')) {
            return $this->redirect($request->query->get('_redirect_to'));
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
            // Write flash message
            $this->dispatchEvent(SyliusCartEvents::ITEM_REMOVE_ERROR, new FlashEvent());

            return $this->redirectToCartSummary();
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);

        // Update models
        $this->dispatchEvent(SyliusCartEvents::ITEM_REMOVE_INITIALIZE, $event);
        $this->dispatchEvent(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $this->dispatchEvent(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $this->dispatchEvent(SyliusCartEvents::ITEM_REMOVE_COMPLETED, new FlashEvent());

        return $this->redirectToCartSummary();
    }
}
