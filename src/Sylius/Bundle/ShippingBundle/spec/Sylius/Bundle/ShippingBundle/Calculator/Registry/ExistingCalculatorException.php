<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Calculator\Registry;

use PHPSpec2\ObjectBehavior;

/**
 * Existing shipping calculator exception.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ExistingCalculatorException extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('custom_calculator');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\Registry\ExistingCalculatorException');
    }

    function it_should_be_an_exception()
    {
        $this->shouldHaveType('Exception');
    }

    function it_should_be_a_invalid_argument_exception()
    {
        $this->shouldHaveType('InvalidArgumentException');
    }
}
