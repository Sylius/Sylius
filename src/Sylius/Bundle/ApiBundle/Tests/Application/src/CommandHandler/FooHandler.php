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
