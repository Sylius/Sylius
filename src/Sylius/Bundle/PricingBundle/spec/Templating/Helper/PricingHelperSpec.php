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
use Sylius\Bundle\PricingBundle\Templating\Helper\PricingHelper;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PricingHelperSpec extends ObjectBehavior
{
    function let(DelegatingCalculatorInterface $calculator)
    {
        $this->beConstructedWith($calculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PricingHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_calculates_price(DelegatingCalculatorInterface $calculator, PriceableInterface $priceable)
    {
        $calculator->calculate($priceable, [])->willReturn(10);

        $this->calculatePrice($priceable)->shouldReturn(10);
    }
}
