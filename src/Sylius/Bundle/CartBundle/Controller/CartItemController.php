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

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartEvents;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Event\CartItemEvents;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Event\FlashEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $emptyItem = $this->createNew($configuration);

        try {
            $item = $this->getResolver()->resolve($emptyItem, $request);
        } catch (ItemResolvingException $exception) {
            $this->eventDispatcher->dispatch(CartItemEvents::ADD_FAILED, new CartItemEvent($cart, $emptyItem));

            return $this->redirectAfterAdd($request);
        }

        $event = new CartItemEvent($cart, $item);

        $this->eventDispatcher->dispatch(CartEvents::CHANGE, new CartEvent($cart));
        $this->eventDispatcher->dispatch(CartItemEvents::PRE_ADD, $event);
        $this->eventDispatcher->dispatch(CartItemEvents::POST_ADD, $event);

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
    public function removeAction(Request $request, $id)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $item = $this->repository->find($id);

        if (!$item || false === $cart->hasItem($item)) {
            // Write flash message
            $this->eventDispatcher->dispatch(CartItemEvents::REMOVE_FAILED, new CartItemEvent($cart, $item));

            return $this->redirectToCartSummary();
        }

        $event = new CartItemEvent($cart, $item);

        $this->eventDispatcher->dispatch(CartEvents::CHANGE, new CartEvent($cart));
        $this->eventDispatcher->dispatch(CartItemEvents::PRE_REMOVE, $event);
        $this->eventDispatcher->dispatch(CartItemEvents::POST_REMOVE, $event);

        return $this->redirectToCartSummary();
    }
}
