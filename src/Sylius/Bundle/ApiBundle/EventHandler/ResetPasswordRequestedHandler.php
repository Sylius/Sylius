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

namespace Sylius\Bundle\ApiBundle\EventHandler;

use Sylius\Bundle\ApiBundle\Command\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\Event\ResetPasswordRequested;
use Symfony\Component\Messenger\MessageBusInterface;

/** @experimental */
final class ResetPasswordRequestedHandler
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(ResetPasswordRequested $resetPasswordRequested): void
    {
        $this->commandBus->dispatch(
            new SendResetPasswordEmail(
                $resetPasswordRequested->email(),
                $resetPasswordRequested->channelCode(),
                $resetPasswordRequested->localeCode()
            )
        );
    }
}
