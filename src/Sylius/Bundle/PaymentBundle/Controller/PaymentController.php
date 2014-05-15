<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\HttpFoundation\Request;

/**
 * Payment controller.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PaymentController extends ResourceController
{
    public function updateStateAction(Request $request, $transition)
    {
        $payment = $this->findOr404($request);

        $stateMachine = $this->get('finite.factory')->get($payment, PaymentTransitions::GRAPH);
        if (!$stateMachine->can($transition)) {
            $this->flashHelper->setFlash('error', 'sylius.payment.transition_fail');

            return $this->redirectHandler->redirectToReferer();
        }

        $stateMachine->apply($transition);

        $this->domainManager->update($payment);

        $this->flashHelper->setFlash('success', 'sylius.payment.'.$transition);

        return $this->redirectHandler->redirectToReferer();
    }
}
