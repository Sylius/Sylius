<?php

namespace Sylius\Bundle\PayumBundle\EventListener;

use Sylius\Bundle\PaymentsBundle\Model\Payment;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\EventDispatcher\GenericEvent;

class ConfirmationListener
{
    /**
     * @param GenericEvent $event
     */
    public function onPostPurchaseStep(GenericEvent $event)
    {
        $step = $event->getSubject();
        /** @var $payment Payment */
        $payment = $event->getArgument('payment');
        /** @var $status StatusRequest */
        $status = $event->getArgument('status');

        if ($status->isSuccess() || $status->isPending()) {
            $step->getCartProvider()->abandonCart();
            $step->complete();
        }

        // Return to payment step
        $step->redirect($step->generateUrl('sylius_checkout_payment'));
    }
}
