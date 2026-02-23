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

namespace Sylius\Bundle\CoreBundle\CommandDispatcher\Shop;

use Sylius\Bundle\CoreBundle\Command\Shop\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShopResetPasswordDispatcher implements ResetPasswordDispatcherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(string $token, string $password): void
    {
        $this->messageBus->dispatch(new ResetPassword($token, $password));
    }
}
