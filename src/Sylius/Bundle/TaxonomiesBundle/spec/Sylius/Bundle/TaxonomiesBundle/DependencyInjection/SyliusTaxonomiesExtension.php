<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomies bundle extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxonomiesExtension extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\DependencyInjection\SyliusTaxonomiesExtension');
    }

    function it_should_be_container_extension()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\DependencyInjection\Extension');
    }
}
