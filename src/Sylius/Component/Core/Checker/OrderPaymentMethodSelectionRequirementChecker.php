<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class OrderPaymentMethodSelectionRequirementChecker implements OrderPaymentMethodSelectionRequirementCheckerInterface
{
    /** @var PaymentMethodsResolverInterface */
    private $paymentMethodsResolver;

    public function __construct(PaymentMethodsResolverInterface $paymentMethodsResolver)
    {
        $this->paymentMethodsResolver = $paymentMethodsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodSelectionRequired(OrderInterface $order): bool
    {
        if ($order->getTotal() <= 0) {
            return false;
        }

        if (!$order->getChannel()->isSkippingPaymentStepAllowed()) {
            return true;
        }

        foreach ($order->getPayments() as $payment) {
            if (count($this->paymentMethodsResolver->getSupportedMethods($payment)) !== 1) {
                return true;
            }
        }

        return false;
    }
}
