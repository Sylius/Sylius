<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Stripe\Action;

use Payum\Core\Security\SensitiveValue;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper;

class CapturePaymentUsingCreditCardAction extends AbstractCapturePaymentAction
{
    /**
     * @var CurrencyHelper
     */
    private $currencyHelper;

    /**
     * @param CurrencyHelper $currencyHelper
     */
    public function __construct(CurrencyHelper $currencyHelper)
    {
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * {@inheritDoc}
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        $order = $payment->getOrder();

        $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

        $creditCard = $obtainCreditCardRequest->getCreditCard();

        $total = $this->currencyHelper->convertAmount($order->getTotal());

        $payment->setDetails(array(
            'card' => new SensitiveValue(array(
                'number'      => $creditCard->getNumber(),
                'expiryMonth' => $creditCard->getExpiryMonth(),
                'expiryYear'  => $creditCard->getExpiryYear(),
                'cvv'         => $creditCard->getSecurityCode()
            )),
            'amount' => round($total / 100, 2),
            'currency' => $order->getCurrency(),
        ));
    }
}
