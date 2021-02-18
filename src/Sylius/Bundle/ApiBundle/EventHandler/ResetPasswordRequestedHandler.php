<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\EventHandler;

use Sylius\Bundle\ApiBundle\Command\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\Event\ResetPasswordRequested;
use Symfony\Component\Messenger\MessageBusInterface;

class ResetPasswordRequestedHandler
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
