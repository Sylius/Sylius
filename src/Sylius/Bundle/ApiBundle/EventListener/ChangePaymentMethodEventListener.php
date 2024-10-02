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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

final class ChangePaymentMethodEventListener
{
    public function __construct(private PaymentRequestCancellerInterface $paymentRequestCanceller)
    {
    }

    public function cancelPaymentRequestsWithDifferentPaymentMethod(ControllerArgumentsEvent $event): void
    {
        $commands = $event->getArguments();
        $method = $event->getRequest()->getMethod();

        if ($method !== Request::METHOD_PATCH) {
            return;
        }

        foreach ($commands as $command) {
            if ($this->isNotValid($command)) {
                continue;
            }

            $this->paymentRequestCanceller->cancelPaymentRequests($command->paymentId, $command->paymentMethodCode);
        }
    }

    private function isNotValid(mixed $command): bool
    {
        return !is_object($command) || (!$command instanceof ChangePaymentMethod && !$command instanceof ChoosePaymentMethod);
    }
}
