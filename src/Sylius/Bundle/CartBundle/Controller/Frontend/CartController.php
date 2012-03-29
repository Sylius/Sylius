<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Controller\Frontend;

use Sylius\Bundle\CartBundle\EventDispatcher\Event\CartOperationEvent;
use Sylius\Bundle\CartBundle\EventDispatcher\Event\FilterCartEvent;
use Sylius\Bundle\CartBundle\EventDispatcher\SyliusCartEvents;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Cart frontend controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ContainerAware
{
    /**
     * Displays cart.
     *
     * @return Response
     */
    public function showAction()
    {
        $cart = $this->container->get('sylius_cart.provider')->getCart();

        $form = $this->container->get('form.factory')->create('sylius_cart');
        $form->setData($cart);

        return $this->container->get('templating')->renderResponse('SyliusCartBundle:Frontend/Cart:show.html.'.$this->getEngine(), array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * Adds item to cart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addItemAction(Request $request)
    {
        $cart = $this->container->get('sylius_cart.provider')->getCart();
        $item = $this->container->get('sylius_cart.resolver')->resolveItemToAdd($request);

        if (!$item) {
            throw new NotFoundHttpException('Requested item could not be added to cart');
        }

        $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::ITEM_ADD, new CartOperationEvent($cart, $item));

        $cartOperator = $this->container->get('sylius_cart.operator');
        $cartOperator->addItem($cart, $item);
        $cartOperator->refresh($cart);

        $errors = $this->container->get('validator')->validate($cart);
        if (0 === count($errors)) {
            $cartOperator->save($cart);
        }

        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }

    /**
     * Removes item from cart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function removeItemAction(Request $request)
    {
        $cart = $this->container->get('sylius_cart.provider')->getCart();
        $item = $this->container->get('sylius_cart.resolver')->resolveItemToRemove($request);

        if (!$item || false === $cart->hasItem($item)) {
            throw new NotFoundHttpException('Requested item could not be removed from cart');
        }

        $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::ITEM_REMOVE, new CartOperationEvent($cart, $item));

        $cartOperator = $this->container->get('sylius_cart.operator');
        $cartOperator->removeItem($cart, $item);
        $cartOperator->refresh($cart);
        $cartOperator->save($cart);

        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }

    /**
     * Saves cart.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $cart = $this->container->get('sylius_cart.provider')->getCart();

        $form = $this->container->get('form.factory')->create('sylius_cart');
        $form->setData($cart);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::CART_SAVE, new FilterCartEvent($cart));

            $cartOperator = $this->container->get('sylius_cart.operator');
            $cartOperator->refresh($cart);
            $cartOperator->save($cart);
        }

        return $this->container->get('templating')->renderResponse('SyliusCartBundle:Frontend/Cart:show.html.'.$this->getEngine(), array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * Clears cart.
     *
     * @return Response
     */
    public function clearAction()
    {
        $cart = $this->container->get('sylius_cart.provider')->getCart();

        $this->container->get('event_dispatcher')->dispatch(SyliusCartEvents::CART_CLEAR, new FilterCartEvent($cart));
        $this->container->get('sylius_cart.operator')->clear($cart);

        return new RedirectResponse($this->container->get('router')->generate('sylius_cart_show'));
    }

    /**
     * Returns templating engine name.
     *
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('sylius_cart.engine');
    }
}
