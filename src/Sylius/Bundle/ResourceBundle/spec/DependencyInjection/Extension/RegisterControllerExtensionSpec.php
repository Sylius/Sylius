<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegisterControllerExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\RegisterControllerExtension');
    }

    function it_is_extension()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ExtensionInterface');
    }

    function it_is_a_supported_extension()
    {
        $this->isSupported(2)->shouldReturn(true);
        $this->isSupported(5)->shouldReturn(false);
    }
}
