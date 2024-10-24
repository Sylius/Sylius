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

namespace Sylius\Component\Core\Checker;

use Symfony\Component\HttpFoundation\RequestStack;

final class CLIContextChecker implements CLIContextCheckerInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function isExecutedFromCLI(): bool
    {
        return null === $this->requestStack->getCurrentRequest();
    }
}
