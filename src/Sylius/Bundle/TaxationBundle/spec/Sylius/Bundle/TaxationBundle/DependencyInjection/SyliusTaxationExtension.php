<?php

namespace spec\Sylius\Bundle\TaxationBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius taxation bundle extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxationExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\DependencyInjection\SyliusTaxationExtension');
    }

    function it_should_be_a_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
