<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Distributor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributor;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

/**
 * @mixin ProportionalIntegerDistributor
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProportionalIntegerDistributorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProportionalIntegerDistributor::class);
    }

    function it_implements_an_integer_proportional_distributor_interface()
    {
        $this->shouldImplement(ProportionalIntegerDistributorInterface::class);
    }

    function it_distributes_an_integer_based_on_elements_participation_in_total()
    {
        $this->distribute([4000, 2000, 2000], 300)->shouldReturn([150, 75, 75]);
    }

    function it_distributes_a_negative_integer_based_on_elements_participation_in_total()
    {
        $this->distribute([4000, 2000, 2000], -300)->shouldReturn([-150, -75, -75]);
    }

    function it_distributes_an_integer_based_on_elements_participation_in_total_even_if_it_can_be_divided_easily()
    {
        $this->distribute([4300, 1400, 2300], 300)->shouldReturn([162, 52, 86]);
    }

    function it_distributes_a_negative_integer_based_on_elements_participation_in_total_even_if_it_can_be_divided_easily()
    {
        $this->distribute([4300, 1400, 2300], -300)->shouldReturn([-162, -52, -86]);
    }

    function it_distributes_an_integer_even_if_its_indivisible_by_number_of_items()
    {
        $this->distribute([4300, 1400, 2300], -299)->shouldReturn([-161, -52, -86]);
    }

    function it_throws_an_exception_if_amount_is_not_integer()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('distribute', [[4300, 1400, 2300], 'string'])
        ;
    }

    function it_throws_an_exception_if_any_of_integers_array_element_is_not_integer()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('distribute', [[4300, '1400', 2300], 300])
        ;
    }
}
