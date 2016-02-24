<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class LexicalContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\LexicalContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_transforms_price_string_to_integer()
    {
        $this->getPriceFromString('10.00')->shouldReturn(1000);
        $this->getPriceFromString('0.30')->shouldReturn(30);
    }

    function it_throws_exception_if_price_string_is_invalid()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Price string should not have more than 2 decimal digits.'))
            ->during('getPriceFromString', ['0.1345'])
        ;
    }

    function it_transforms_percentage_string_to_float()
    {
        $this->getPercentageFromString('10')->shouldReturn(0.1);
    }
}
