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

namespace Sylius\Component\Resource\StateMachine;

use SM\StateMachine\StateMachineInterface as BaseStateMachineInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface StateMachineInterface extends BaseStateMachineInterface
{
    /**
     * Returns the possible transition from given state
     * Returns null if no transition is possible
     *
     * @param string $fromState
     *
     * @return string|null
     */
    public function getTransitionFromState($fromState);

    /**
     * Returns the possible transition to the given state
     * Returns null if no transition is possible
     *
     * @param string $toState
     *
     * @return string|null
     */
    public function getTransitionToState($toState);
}
