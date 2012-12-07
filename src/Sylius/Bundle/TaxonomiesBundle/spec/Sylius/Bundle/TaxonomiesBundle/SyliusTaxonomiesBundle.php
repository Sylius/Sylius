<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomies bundle spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxonomiesBundle extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\SyliusTaxonomiesBundle');
    }

    function it_should_be_a_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }
}
