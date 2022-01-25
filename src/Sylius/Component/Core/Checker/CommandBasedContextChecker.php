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

namespace Sylius\Component\Core\Checker;

use Sylius\Component\Core\Context\ProcessContextInterface;

final class CommandBasedContextChecker implements CommandBasedContextCheckerInterface
{
    public function __construct(
      private string $runningEnvironment
    ) { }

    public function isRunningFromCommand(): bool
    {
        return $this->runningEnvironment !== 'test' && \php_sapi_name() === ProcessContextInterface::CLI;
    }
}
