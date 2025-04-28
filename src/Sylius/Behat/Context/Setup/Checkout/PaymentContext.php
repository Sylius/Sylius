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

namespace Sylius\Behat\Context\Setup\Checkout;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Exception\SharedStorageElementNotFoundException;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class PaymentContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private MessageBusInterface $commandBus,
    ) {
    }

    #[Given('I chose :paymentMethod payment method')]
    #[Given('the customer chose :paymentMethod payment method')]
    #[Given('the visitor chose :paymentMethod payment method')]
    public function iChosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->choosePaymentMethod($paymentMethod);
    }

    /** @throws SharedStorageElementNotFoundException */
    public function choosePaymentMethod(?PaymentMethodInterface $paymentMethod = null): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $paymentMethodCode = $paymentMethod?->getCode() ?? $this->sharedStorage->get('payment_method')->getCode();

        $this->commandBus->dispatch(new ChoosePaymentMethod(
            $order->getTokenValue(),
            $order->getPayments()->first()->getId(),
            $paymentMethodCode,
        ));
    }
}
