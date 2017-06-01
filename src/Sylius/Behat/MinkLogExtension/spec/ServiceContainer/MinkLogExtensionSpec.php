<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\MinkLogExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;

/**
 * @mixin \Sylius\Behat\MinkLogExtension\ServiceContainer\MinkLogExtension
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MinkLogExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\MinkLogExtension\ServiceContainer\MinkLogExtension');
    }

    function it_is_a_testwork_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_named_log()
    {
        $this->getConfigKey()->shouldReturn('log');
    }
}
