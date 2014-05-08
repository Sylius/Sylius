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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Component\Form\FormInterface;

/**
 * The shipping step of checkout.
 *
 * Based on the user address, we present the available shipping methods,
 * and ask him to select his preferred one.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingStep extends CheckoutStep
{
    /**
     * @var null|ZoneInterface
     */
    private $zones;

    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        $form = $this->createCheckoutShippingForm($order);

        if (empty($this->zones)) {
            return $this->proceed($context->getPreviousStep()->getName());
        }

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        $form = $this->createCheckoutShippingForm($order);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);

            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:shipping.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context,
        ));
    }

    protected function createCheckoutShippingForm(OrderInterface $order)
    {
        $this->zones = $this->getZoneMatcher()->matchAll($order->getShippingAddress());

        if (empty($this->zones)) {
            $this->get('session')->getFlashBag()->add('error', 'sylius.checkout.shipping.error');
        }

        return $this->createForm('sylius_checkout_shipping', $order, array(
            'criteria' => array('zone' => !empty($this->zones) ? array_map(function ($zone) {
                return $zone->getId();
            }, $this->zones) : null)
        ));
    }
}
