<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Process;

use PhpSpec\ObjectBehavior;

class InstallerScenarioSpec extends ObjectBehavior
{
    function it_is_a_process_step()
    {
        $this->shouldBeAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface');
    }
}
