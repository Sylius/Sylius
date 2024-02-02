<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderPaymentsExtension extends AbstractExtension
{
    public function __construct(private PaymentMethodsResolverInterface $paymentMethodsResolver)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_order_can_be_paid', [$this, 'allNewPaymentsCanBePaid']),
        ];
    }

    public function allNewPaymentsCanBePaid(OrderInterface $order): bool
    {
        $newPayments = $order->getPayments()->filter(function (PaymentInterface $payment) {
            return $payment->getState() === PaymentInterface::STATE_NEW;
        });

        if ($newPayments->isEmpty()) {
            return false;
        }

        foreach ($newPayments as $payment) {
            if (0 === count($this->paymentMethodsResolver->getSupportedMethods($payment))) {
                return false;
            }
        }

        return true;
    }
}
