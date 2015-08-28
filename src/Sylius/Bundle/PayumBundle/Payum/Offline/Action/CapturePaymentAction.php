<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Offline\Action;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Offline\Constants;
use Sylius\Bundle\PayumBundle\Payum\Action\GenericCapturePaymentAction;

/**
 * @author Antonio Peric <antonio@locastic.com>
 */
class CapturePaymentAction extends GenericCapturePaymentAction
{
    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $payment = $request->getModel();
        if ($payment->getDetails()) {
            return;
        }

        $payment->setDetails(array(
            Constants::FIELD_PAID => false,
            Constants::FIELD_STATUS => Constants::STATUS_PENDING
        ));
    }
}
