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

use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

class CapturePaymentUsingCreditCardAction extends AbstractCapturePaymentAction
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
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

        $total = $this->currencyConverter->convert($order->getTotal(), $order->getCurrency());

        $payment->setDetails(array(
            'amount' => round($total / 100, 2),
            'currency' => $order->getCurrency(),
        ));
    }
}
