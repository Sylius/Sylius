<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\MinkLogExtension\Listener;

use Behat\Mink\Mink;
use PhpSpec\ObjectBehavior;

/**
 * @mixin \Sylius\Behat\MinkLogExtension\Listener\FailedStepListener
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FailedStepListenerSpec extends ObjectBehavior
{
    function let(Mink $mink)
    {
        $this->beConstructedWith($mink, 'logDirectory', true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\MinkLogExtension\Listener\FailedStepListener');
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }
}
