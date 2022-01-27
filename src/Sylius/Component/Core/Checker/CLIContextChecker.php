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

final class CLIContextChecker implements CLIContextCheckerInterface
{
    private const CLI = 'cli';

    public function __construct(
        private string $runningEnvironment,
        private array $restrictedEnvironments
    ) { }

    public function isExecutedFromCLI(): bool
    {
        return !in_array($this->runningEnvironment, $this->restrictedEnvironments) && \php_sapi_name() === self::CLI;
    }
}
