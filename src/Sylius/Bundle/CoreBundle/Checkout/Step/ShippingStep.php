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
 * The shipping step of checkout.
 * Based on the user address, we present the available shipping methods,
 * and ask him to select his preffered one.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $form = $this->createCheckoutShippingForm($context);

        return $this->renderStep($context, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createCheckoutShippingForm($context);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $data = $form->getData();

            $shippingMethod = $data['shippingMethod'];

            $context->getStorage()->set('shipping_method', $shippingMethod->getId());

            return $this->complete();
        }

        return $this->renderStep($context, $form);
    }

    private function renderStep(ProcessContextInterface $context, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:shipping.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    private function createCheckoutShippingForm(ProcessContextInterface $context)
    {
        $shippingAddress = $this->getAddress($context->getStorage()->get('shipping_address'));
        $zone = $this->get('sylius.zone_matcher')->match($shippingAddress);

        return $this->createForm('sylius_checkout_shipping', null, array(
            'shippables' => $this->getCartProvider()->getCart(),
            'criteria'   => array('zone' => $zone)
        ));
    }
}
