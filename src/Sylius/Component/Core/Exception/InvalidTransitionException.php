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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class InvalidTransitionException extends SMException
{
    /**
     * @param string $transition
     */
    public function __construct($transition)
    {
        parent::__construct(sprintf('Transition "%s" is invalid for this state machine.', $transition));
    }
}
