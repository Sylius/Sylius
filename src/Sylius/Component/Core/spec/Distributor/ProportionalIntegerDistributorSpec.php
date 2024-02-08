<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Distributor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

final class ProportionalIntegerDistributorSpec extends ObjectBehavior
{
    function it_implements_an_integer_proportional_distributor_interface(): void
    {
        $this->shouldImplement(ProportionalIntegerDistributorInterface::class);
    }

    function it_distributes_an_integer_based_on_elements_participation_in_total(): void
    {
        $this->distribute([4000, 2000, 2000], 300)->shouldReturn([150, 75, 75]);
    }

    function it_distributes_a_negative_integer_based_on_elements_participation_in_total(): void
    {
        $this->distribute([4000, 2000, 2000], -300)->shouldReturn([-150, -75, -75]);
    }

    function it_distributes_an_integer_based_on_elements_participation_in_total_even_if_it_can_be_divided_easily(): void
    {
        $this->distribute([4300, 1400, 2300], 300)->shouldReturn([162, 52, 86]);
    }

    function it_distributes_a_negative_integer_based_on_elements_participation_in_total_even_if_it_can_be_divided_easily(): void
    {
        $this->distribute([4300, 1400, 2300], -300)->shouldReturn([-162, -52, -86]);
    }

    function it_distributes_an_integer_even_if_its_indivisible_by_number_of_items(): void
    {
        $this->distribute([4300, 1400, 2300], -299)->shouldReturn([-161, -52, -86]);
    }

    function it_distributes_an_integer_even_for_non_distributable_items(): void
    {
        $this->distribute([0], -299)->shouldReturn([0]);
    }

    function it_keeps_original_keys_after_computation(): void
    {
        $this->distribute([
            1 => 4000,
            3 => 2000,
            6 => 2000,
        ], 300)->shouldReturn([
            1 => 150,
            3 => 75,
            6 => 75,
        ]);
    }

    function it_throws_an_exception_if_any_of_integers_array_element_is_not_integer(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('distribute', [[4300, '1400', 2300], 300])
        ;
    }
}
