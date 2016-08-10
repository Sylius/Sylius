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

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sylius\Component\Cart\CartActions;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Event\FlashEvent;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class CartItemController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getItemQuantityModifier()->modify($newResource, 1);

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $newResource = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            $cart = $this->getCurrentCart();
            $this->resolveCartItem($cart, $newResource);

            $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($newResource, Response::HTTP_CREATED));
            }

            $this->getEventDispatcher()->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));

            $cartManager = $this->getCartManager();
            $cartManager->persist($cart);
            $cartManager->flush();

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                $this->metadata->getName() => $newResource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(CartActions::ADD . '.html'))
        ;

        return $this->viewHandler->handle($configuration, $view);
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
    public function removeAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $item = $this->findOr404($configuration);

        $eventDispatcher = $this->getEventDispatcher();

        if (!$item || false === $cart->hasItem($item)) {
            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_ERROR, new FlashEvent());

            return $this->redirectToCartSummary($configuration);
        }

        $event = new CartItemEvent($cart, $item);

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_INITIALIZE, $event);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_REMOVE_COMPLETED, new FlashEvent());

        return $this->redirectToCartSummary($configuration);
    }

    /**
     * @param CartInterface $cart
     * @param CartItemInterface $item
     */
    private function resolveCartItem(CartInterface $cart, CartItemInterface $item)
    {
        foreach ($cart->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $this->getItemQuantityModifier()->modify($existingItem, $existingItem->getQuantity() + $item->getQuantity());

                return;
            }
        }

        $cart->addItem($item);
    }

    /**
     * @return OrderItemQuantityModifierInterface
     */
    private function getItemQuantityModifier()
    {
        return $this->get('sylius.order_item_quantity_modifier');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getCartManager()
    {
        return $this->get('sylius.manager.cart');
    }
}
