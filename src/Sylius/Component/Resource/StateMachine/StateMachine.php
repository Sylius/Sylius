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

use Finite\StateMachine\StateMachine as BaseStateMachine;

/**
 * Sylius State Machine
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class StateMachine extends BaseStateMachine implements StateMachineInterface
{
    /**
     * @{inheritDoc}
     */
    public function getTransitionToState($toState, $fromState = null)
    {
        if (null === $fromState) {
            $fromState = $this->getCurrentState()->getName();
        }

        foreach ($this->getTransitions() as $transitionName) {
            $transition = $this->getTransition($transitionName);
            if (in_array($fromState, $transition->getInitialStates()) && $toState === $transition->getState()) {
                return $transition->getName();
            }
        }

        return null;
    }
}
