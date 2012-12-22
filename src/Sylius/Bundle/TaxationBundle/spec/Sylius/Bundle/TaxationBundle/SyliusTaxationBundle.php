<?php

namespace spec\Sylius\Bundle\TaxationBundle;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius taxation bundle spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxationBundle extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\SyliusTaxationBundle');
    }

    function it_should_be_a_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }
}
