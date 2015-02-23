<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidationGroupExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ValidationGroupExtension');
    }

    function it_is_extension()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ExtensionInterface');
    }


    function it_is_a_supported_extension()
    {
        $this->isSupported(8)->shouldReturn(true);
        $this->isSupported(5)->shouldReturn(false);
    }
}
