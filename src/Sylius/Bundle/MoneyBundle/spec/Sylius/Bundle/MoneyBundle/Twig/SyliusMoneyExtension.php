<?php

namespace spec\Sylius\Bundle\MoneyBundle\Twig;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius money extension for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusMoneyExtension extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
