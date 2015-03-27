<?php

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegisterFormTypeExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\RegisterFormTypeExtension');
    }

    function it_is_extension()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\ExtensionInterface');
    }

    function it_is_a_supported_extension()
    {
        $this->isSupported(4)->shouldReturn(true);
        $this->isSupported(16)->shouldReturn(false);
    }


}
