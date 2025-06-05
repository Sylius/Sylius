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

use Sylius\Bundle\ApiBundle\Application\Command\BarCommand;
use Sylius\Bundle\ApiBundle\Application\Entity\Bar;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class BarHandler
{
    public function __invoke(BarCommand $command): Bar
    {
        $bar = new Bar();
        $bar->setFoo($command->foo);
        $bar->setBar($command->bar);

        return $bar;
    }
}
