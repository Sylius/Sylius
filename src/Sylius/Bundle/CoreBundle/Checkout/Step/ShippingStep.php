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
        $form = $this->createCheckoutShippingForm();

        return $this->renderStep($context, $this->getCurrentCart(), $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();
        $form = $this->createCheckoutShippingForm();

        $cart = $this->getCurrentCart();

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($cart);
            $this->getManager()->flush();

            return $this->complete();
        }

        return $this->renderStep($context, $cart, $form);
    }

    private function renderStep(ProcessContextInterface $context, OrderInterface $cart, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:shipping.html.twig', array(
            'cart'    => $cart,
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    private function createCheckoutShippingForm()
    {
        $cart = $this->getCurrentCart();
        $zone = $this->getZoneMatcher()->match($cart->getShippingAddress());

        if (!$cart->hasShipments()) {
            $this
                ->getInventoryUnitsFactory()
                ->createInventoryUnits($cart)
            ;

            $this
                ->getShipmentFactory()
                ->createShipment($cart)
            ;
        }

        return $this->createForm('sylius_checkout_shipping', $cart, array(
            'criteria'  => array('zone' => $zone)
        ));
    }


    private function getInventoryUnitsFactory()
    {
        return $this->get('sylius.order_processing.inventory_units_factory');
    }

    private function getShipmentFactory()
    {
        return $this->get('sylius.order_processing.shipment_factory');
    }
}
