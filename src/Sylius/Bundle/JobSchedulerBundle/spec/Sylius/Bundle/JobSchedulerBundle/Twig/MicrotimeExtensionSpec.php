<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class MicrotimeExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Twig\MicrotimeExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldImplement('\Twig_Extension');
    }
}
