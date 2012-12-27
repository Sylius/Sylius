<?php

namespace spec\Sylius\Bundle\SettingsBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius settings bundle extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSettingsExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\DependencyInjection\SyliusSettingsExtension');
    }

    function it_should_be_a_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
