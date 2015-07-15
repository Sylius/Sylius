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
use Payum\Core\Request\Capture;
use Sylius\Bundle\PayumBundle\Payum\Action\GenericCapturePaymentAction;
use Sylius\Component\Payment\Model\PaymentInterface;

class CapturePaymentAction extends GenericCapturePaymentAction
{
    /**
     * {@inheritDoc}
     *
     * @param $request Capture
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        if ($payment->getDetails()) {
            return;
        }

        $payment->setDetails(array('captured' => true));
    }
}
