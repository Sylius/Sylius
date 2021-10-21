<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Application\CommandHandler;

use Sylius\Bundle\ApiBundle\Application\Command\FooCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FooHandler implements MessageHandlerInterface
{
    public function __invoke(FooCommand $command): void
    {
        return;
    }
}
