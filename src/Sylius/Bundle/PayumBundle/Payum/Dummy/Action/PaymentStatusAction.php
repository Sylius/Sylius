<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Dummy\Action;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\PaymentStatusAction as BasePaymentStatusAction;
use Sylius\Component\Payment\Model\PaymentInterface;

class PaymentStatusAction extends BasePaymentStatusAction
{
    /**
     * {@inheritDoc}
     *
     * @param $request GetStatusInterface
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        $paymentDetails = $payment->getDetails();

        if (empty($paymentDetails)) {
            $request->markNew();

            return;
        }

        if (isset($paymentDetails['captured'])) {
            $request->markCaptured();

            return;
        }

        $request->markUnknown();
    }
}
