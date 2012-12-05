<?php

namespace spec\Sylius\Bundle\CartBundle;

use PHPSpec2\ObjectBehavior;

/**
 * Cart bundle spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartBundle extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\SyliusCartBundle');
    }

    function it_should_be_symfony_bundle()
    {
        $this->shouldHaveType('Symfony\Component\HttpKernel\Bundle\Bundle');
    }
}
