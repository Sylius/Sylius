<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;

/**
 * The addressing step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $form = $this->createCheckoutAddressingForm();

        return $this->renderStep($context, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createCheckoutAddressingForm();

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $cart = $this->getCurrentCart();
            $cart->getShippingAddress()->setUser($this->getUser());
            $cart->getBillingAddress()->setUser($this->getUser());

            $this->getManager()->persist($cart);
            $this->getManager()->flush();

            return $this->complete();
        }

        return $this->renderStep($context, $form);
    }

    private function renderStep(ProcessContextInterface $context, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:addressing.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));

    }

    private function createCheckoutAddressingForm()
    {
        return $this->createForm('sylius_checkout_addressing', $this->getCurrentCart());
    }
}
