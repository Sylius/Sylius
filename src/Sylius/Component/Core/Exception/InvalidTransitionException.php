<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Exception;

use SM\SMException;
use SM\StateMachine\StateMachineInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class InvalidTransitionException extends SMException
{
    /**
     * @param string $transition
     * @param StateMachineInterface $stateMachine
     */
    public function __construct($transition, StateMachineInterface $stateMachine)
    {
        parent::__construct(
            sprintf(
                'Transition "%s" is invalid for "%s" state machine. Possible transitions are: %s.',
                $transition,
                $stateMachine->getGraph(),
                implode($stateMachine->getPossibleTransitions(), ' and ')
            )
        );
    }
}
