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
        $form = $this->createCheckoutPaymentForm();

        return $this->renderStep($context, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createCheckoutPaymentForm();

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($this->getCurrentCart());
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

    private function createCheckoutPaymentForm()
    {
        return $this->createForm('sylius_checkout_payment', $this->getCurrentCart());
    }
}
