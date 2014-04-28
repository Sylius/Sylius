<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\StateMachine;

use Finite\StateMachine\StateMachineInterface as BaseStateMachineInterface;

/**
 * Sylius State Machine
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface StateMachineInterface extends BaseStateMachineInterface
{
    /**
     * Returns the possible transition between given states
     * Returns null if no transition is possible
     *
     * @param string      $toState
     * @param string|null $fromState
     *
     * @return string|null
     */
    public function getTransitionToState($toState, $fromState = null);
}
