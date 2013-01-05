<?php

namespace spec\Sylius\Bundle\InventoryBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius inventory extension spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\DependencyInjection\SyliusInventoryExtension');
    }

    function it_should_be_a_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
