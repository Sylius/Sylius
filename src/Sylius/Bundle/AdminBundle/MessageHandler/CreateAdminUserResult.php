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

namespace Sylius\Bundle\AdminBundle\MessageHandler;


class CreateAdminUserResult
{
    public function __construct(private bool $hasViolations, private iterable $violationMessages = [])
    {
    }

    public function hasViolations(): bool
    {
        return $this->hasViolations;
    }

    public function getViolationMessages(): array
    {
        return [...$this->violationMessages];
    }
}
