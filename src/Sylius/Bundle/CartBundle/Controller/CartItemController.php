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

use FOS\RestBundle\View\View;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Event\FlashEvent;
use Sylius\Component\Resource\Metadata\MetadataInterface;
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

        $itemQuantityModifier = $this->get('sylius.order_item_quantity_modifier');
        $itemQuantityModifier->modify($newResource, 1);

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

            $isItemInCart = false;
            foreach ($cart->getItems() as $item) {
                if ($newResource->equals($item)) {
                    $itemQuantityModifier->modify($item, $item->getQuantity() + $newResource->getQuantity());
                    $isItemInCart = true;

                    break;
                }
            }

            if (!$isItemInCart) {
                $cart->addItem($newResource);
                $this->repository->add($newResource);

                $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

                if (!$configuration->isHtmlRequest()) {
                    return $this->viewHandler->handle($configuration, View::create($newResource, Response::HTTP_CREATED));
                }
            }

            $translatedMessage = $this->translateMessage('sylius.cart.item_add_completed', $this->metadata);
            $this->addFlash('success', $translatedMessage);

            $orderRecalculator = $this->get('sylius.order_processing.order_recalculator');
            $orderRecalculator->recalculate($cart);

            $cartManager = $this->get('sylius.manager.cart');
            $cartManager->persist($cart);
            $cartManager->flush();

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $newResource,
                $this->metadata->getName() => $newResource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::CREATE . '.html'))
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
     * @param string $flashMessage
     * @param MetadataInterface $metadata
     *
     * @return string
     */
    private function translateMessage($flashMessage, MetadataInterface $metadata)
    {
        return $this->get('translator')->trans($flashMessage, ['%resource%' => ucfirst($metadata->getHumanizedName())], 'flashes');
    }
}
