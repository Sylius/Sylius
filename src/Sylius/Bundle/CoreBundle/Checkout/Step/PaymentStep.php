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

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;

/**
 * The payment step of checkout.
 * User selects the payment method.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PaymentStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $cart = $this->getCurrentCart();
        $form = $this->createCheckoutPaymentForm($cart);

        return $this->renderStep($context, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $cart = $this->getCurrentCart();
        $form = $this->createCheckoutPaymentForm($cart);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($cart);
            $this->getManager()->flush();

            return $this->complete();
        }

        return $this->renderStep($context, $form);
    }

    private function renderStep(ProcessContextInterface $context, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:payment.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    private function createCheckoutPaymentForm(OrderInterface $cart)
    {
        if (null === $cart->getPayment()) {
            $this->getPaymentProcessor()->createPayment($cart);
        }

        return $this->createForm('sylius_checkout_payment', $cart);
    }

    private function getPaymentProcessor()
    {
        return $this->get('sylius.order_processing.payment_processor');
    }
}
