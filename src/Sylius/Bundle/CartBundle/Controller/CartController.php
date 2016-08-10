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
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Event\FlashEvent;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Default cart controller.
 * It extends the format agnostic resource controller.
 * Resource controller class provides several actions and methods for creating
 * pages and api for your cart system.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartController extends Controller
{
    /**
     * Displays current cart summary page.
     * The parameters includes the form created from `sylius_cart` type.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function summaryAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $form = $this->resourceFormFactory->create($configuration, $cart);

        $view = View::create()
            ->setTemplate($configuration->getTemplate('summary.html'))
            ->setData([
                'cart' => $cart,
                'form' => $form->createView(),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * This action is used to submit the cart summary form.
     * If the form and updated cart are valid, it refreshes
     * the cart data and saves it using the operator.
     *
     * If there are any errors, it displays the cart summary page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $form = $this->resourceFormFactory->create($configuration, $cart);

        if ($form->handleRequest($request)->isValid()) {
            $event = new CartEvent($cart);

            $eventDispatcher = $this->getEventDispatcher();

            $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));

            // Update models
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_COMPLETED, new FlashEvent());

            return $this->redirectToCartSummary($configuration);
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('summary.html'))
            ->setData([
                'cart' => $cart,
                'form' => $form->createView(),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function clearAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::DELETE);
        $resource = $this->getCurrentCart();

        $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource);

        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToIndex($configuration, $resource);
        }

        $this->repository->remove($resource);
        $this->eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource);

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }
}
