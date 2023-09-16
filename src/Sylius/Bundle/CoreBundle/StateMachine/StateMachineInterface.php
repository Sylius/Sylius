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

namespace Sylius\Bundle\CoreBundle\StateMachine;

interface StateMachineInterface
{
    public function can(object $subject, string $graphName, string $transition): bool;

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void;

    /**
     * @return array<TransitionInterface>
     */
    public function getEnabledTransition(object $subject, string $graphName): array;
}
