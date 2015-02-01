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

use Payum\Core\Exception\LogicException;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Payment\Model\PaymentInterface;
use Payum\Offline\Constants;


/**
 * @author Antonio Peric <antonio@locastic.com>
 */
class CapturePaymentAction extends AbstractCapturePaymentAction
{
    /**
     * @param PaymentInterface $payment
     * @param TokenInterface   $token
     *
     * @throws LogicException
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        $details = array();
        $details[Constants::FIELD_PAID] = false;

        $payment->setDetails($details);
    }
}
