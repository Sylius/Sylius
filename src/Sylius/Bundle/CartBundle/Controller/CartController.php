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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cart controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ResourceController
{
    /**
     * Displays cart.
     *
     * @param Request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $cart = $this->getCurrentCart();
        $form = $this->createForm('sylius_cart', $cart);

        return $this->renderResponse('show.html', array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
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
        $cart = $this->getCurrentCart();

        $form = $this
            ->createForm('sylius_cart', $cart)
            ->bind($request)
        ;

        if ($form->isValid()) {
            $this
                ->getOperator()
                ->refresh($cart)
                ->save($cart)
            ;

            $this->setFlash('success', 'sylius_cart.flashes.saved');
        }

        return $this->renderResponse('show.html', array(
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
        $this
            ->getOperator()
            ->clear($this->getCurrentCart())
        ;

        $this->setFlash('success', 'sylius_cart.flashes.cleared');

        return $this->redirectToCart();
    }

    /**
     * Redirect to show cart action.
     *
     * @return RedirectResponse
     */
    protected function redirectToCart()
    {
        return $this->redirect($this->generateUrl($this->getCartRoute()));
    }

    /**
     * Cart show action route.
     *
     * @return string
     */
    protected function getCartRoute()
    {
        return 'sylius_cart_show';
    }

    /**
     * Get current cart.
     *
     * @return CartInterface
     */
    protected function getCurrentCart()
    {
        return $this
            ->getProvider()
            ->getCart()
        ;
    }

    /**
     * Get cart provider.
     *
     * @return CartProviderInterface
     */
    protected function getProvider()
    {
        return $this->get('sylius_cart.provider');
    }

    /**
     * Get cart operator.
     *
     * @return CartOperatorInterface
     */
    protected function getOperator()
    {
        return $this->get('sylius_cart.operator');
    }
}
