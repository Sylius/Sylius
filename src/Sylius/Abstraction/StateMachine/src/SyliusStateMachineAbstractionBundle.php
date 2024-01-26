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

namespace Sylius\Abstraction\StateMachine;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SyliusStateMachineAbstractionBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
