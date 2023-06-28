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

namespace Sylius\Bundle\CoreBundle\MessageDispatcher;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Symfony\Component\Messenger\MessageBusInterface;

final class ResetPasswordDispatcher implements ResetPasswordDispatcherInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function dispatch(string $token, string $password): void
    {
        $this->messageBus->dispatch(new ResetPassword($token, $password));
    }
}
