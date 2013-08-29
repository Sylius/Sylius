<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Process\Step;

use PHPSpec2\ObjectBehavior;

class CheckStep extends ObjectBehavior
{
    function it_is_a_process_step()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Step\ControllerStep');
    }
}
