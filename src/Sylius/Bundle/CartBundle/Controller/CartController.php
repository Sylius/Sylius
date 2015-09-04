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
use Sylius\Component\Cart\Event\CartEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default cart controller.
 * It extends the format agnostic resource controller.
 *
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
    public function summaryAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $form = $this->createResourceForm($configuration, $cart);

        $view = View::create()
            ->setTemplate($configuration->getTemplate('summary.html'))
            ->setData(array(
                'cart' => $cart,
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        $form = $this->createResourceForm($configuration, $cart);

        if ($form->handleRequest($request)->isValid()) {
            $event = new CartEvent($cart);

            $this->eventDispatcher->dispatch(CartEvents::CHANGE, $event);
            $this->eventDispatcher->dispatch(CartEvents::PRE_SAVE, $event);

            $this->manager->persist($cart);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(CartEvents::POST_SAVE, $event);

            return $this->redirectToCartSummary();
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('summary.html'))
            ->setData(array(
                'cart' => $cart,
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
    }

    /**
     * Clears the current cart using the operator.
     * By default it redirects to cart summary page.
     *
     * @return Response
     */
    public function clearAction()
    {
        $cart = $this->getCurrentCart();
        $event = new CartEvent($cart);

        $this->eventDispatcher->dispatch(CartEvents::PRE_CLEAR, $event);

        $this->manager->remove($cart);
        $this->manager->flush();

        $this->eventDispatcher->dispatch(CartEvents::POST_CLEAR, $event);

        return $this->redirectToCartSummary();
    }
}
