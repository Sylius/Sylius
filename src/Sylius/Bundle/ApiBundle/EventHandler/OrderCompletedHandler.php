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

namespace Sylius\Bundle\ApiBundle\EventHandler;

use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\Event\OrderCompleted;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCompletedHandler
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function __invoke(OrderCompleted $orderCompleted): void
    {
        $this->commandBus->dispatch(new SendOrderConfirmation($orderCompleted->orderToken()));
    }
}
