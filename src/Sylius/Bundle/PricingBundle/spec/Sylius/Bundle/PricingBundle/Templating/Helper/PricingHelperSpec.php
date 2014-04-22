<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PricingHelperSpec extends ObjectBehavior
{
    function let(DelegatingCalculatorInterface $calculator)
    {
        $this->beConstructedWith($calculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Templating\Helper\PricingHelper');
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }
}
