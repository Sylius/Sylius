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

interface TransitionInterface
{
    public function getName(): string;

    /**
     * @return array<string>|null
     */
    public function getFroms(): ?array;

    /**
     * @return array<string>|null
     */
    public function getTos(): ?array;
}
