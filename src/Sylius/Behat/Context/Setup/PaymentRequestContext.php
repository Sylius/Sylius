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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentRequestContext implements Context
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    /**
     * @Given the payment request action :action has been executed for order :order with the payment method :paymentMethod
     */
    public function thePaymentRequestActionHasBeenExecutedForOrderWithThePaymentMethod(
        string $action,
        OrderInterface $order,
        PaymentMethodInterface $paymentMethod
    ): void {
        $addPaymentRequest = new AddPaymentRequest(
            paymentId: $order->getLastPayment()->getId(),
            paymentMethodCode: $paymentMethod->getCode(),
            action: $action,
        );

        $this->commandBus->dispatch($addPaymentRequest);
    }
}