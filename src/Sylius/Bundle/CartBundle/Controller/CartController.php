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
 * Default cart controller.
 * It extends the format agnostic resource controller.
 * Resource controller class provides several actions and methods for creating
 * pages and api for your cart system.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ResourceController
{
    /**
     * Displays current cart summary page.
     * The parameters includes the form created from `sylius_cart` type.
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
     * Clears the current cart using the operator.
     * By default it redirects to cart summary page.
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
     * Redirect to show summary page.
     *
     * @return RedirectResponse
     */
    protected function redirectToCart()
    {
        return $this->redirect($this->generateUrl($this->getCartRoute()));
    }

    /**
     * Cart summary page route.
     *
     * @return string
     */
    protected function getCartRoute()
    {
        return 'sylius_cart_show';
    }

    /**
     * Get current cart instance.
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
     * Get cart provider service.
     *
     * @return CartProviderInterface
     */
    protected function getProvider()
    {
        return $this->get('sylius_cart.provider');
    }

    /**
     * Get cart operator service.
     *
     * @return CartOperatorInterface
     */
    protected function getOperator()
    {
        return $this->get('sylius_cart.operator');
    }
}
