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

        $eventDispatcher = $this->getEventDispatcher();

        try {
            $item = $this->getResolver()->resolve($emptyItem, $request);
        } catch (ItemResolvingException $exception) {
            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_ERROR, new FlashEvent($exception->getMessage()));

            return $this->redirectAfterAdd($request);
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);
        $event->isValid(false);

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_INITIALIZE, $event);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_COMPLETED, new FlashEvent());

        return $this->redirectAfterAdd($request);
    }

    /**
     * Removes item from cart.
     * It takes an item id as an argument.
     *
     * If the item is found and the current user cart contains that item,
     * it will be removed and the cart - refreshed and saved.
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction($id, Request $request)
    {
        $cart = $this->getCurrentCart();
        $item = $this->getRepository()->find($id);

        $eventDispatcher = $this->getEventDispatcher();

        if (!$item || false === $cart->hasItem($item)) {
            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_ERROR, new FlashEvent());

            return $this->redirectAfterRemove($request);
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_INITIALIZE, $event);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_COMPLETED, new FlashEvent());

        return $this->redirectAfterRemove($request);
    }

    /**
     * Redirect to specific URL or to cart after adding an item.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function redirectAfterAdd(Request $request)
    {
        return $this->redirectAfterAction($request);
    }

    /**
     * Redirect to specific URL or to cart after removing an item.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function redirectAfterRemove(Request $request)
    {
        return $this->redirectAfterAction($request);
    }

    /**
     * Default redirect to specific URL or to cart.
     * Use <code>_redirect_to</code> key in query or post data to designate
     * a specific target page.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function redirectAfterAction(Request $request)
    {
        $redirectUrl = $request->query->get('_redirect_to', $request->request->get('_redirect_to'));
        if (null !== $redirectUrl) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirectToCartSummary();
    }
}
