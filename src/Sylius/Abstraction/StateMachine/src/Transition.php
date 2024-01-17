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

final class Transition implements TransitionInterface
{
    /**
     * @param array<string>|null $froms
     * @param array<string>|null $tos
     */
    public function __construct(
        private string $name,
        private ?array $froms,
        private ?array $tos,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFroms(): ?array
    {
        return $this->froms;
    }

    public function getTos(): ?array
    {
        return $this->tos;
    }
}
