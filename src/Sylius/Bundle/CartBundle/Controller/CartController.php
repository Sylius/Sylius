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
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Event\FlashEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function summaryAction()
    {
        $cart = $this->getCurrentCart();
        $form = $this->createForm('sylius_cart', $cart);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('summary.html'))
            ->setData(array(
                'cart' => $cart,
                'form' => $form->createView(),
            ))
        ;

        return $this->handleView($view);
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
        $cart = $this->getCurrentCart();
        $form = $this->createForm('sylius_cart', $cart);

        if ($form->handleRequest($request)->isValid()) {
            $event = new CartEvent($cart);
            $event->isFresh(true);

            $eventDispatcher = $this->getEventDispatcher();

            $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));

            // Update models
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_COMPLETED, new FlashEvent());

            return $this->redirectToCartSummary();
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('summary.html'))
            ->setData(array(
                'cart' => $cart,
                'form' => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Clears the current cart using the operator.
     * By default it redirects to cart summary page.
     *
     * @return Response
     */
    public function clearAction()
    {
        $eventDispatcher = $this->getEventDispatcher();

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_INITIALIZE, new CartEvent($this->getCurrentCart()));

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_COMPLETED, new FlashEvent());

        return $this->redirectToCartSummary();
    }
}
