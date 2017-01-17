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
use Sylius\Component\Core\Distributor\IntegerDistributor;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class IntegerDistributorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IntegerDistributor::class);
    }

    function it_implements_an_integer_distributor_interface()
    {
        $this->shouldImplement(IntegerDistributorInterface::class);
    }

    function it_distributes_simple_integers()
    {
        $this->distribute(0, 4)->shouldReturn([0, 0, 0, 0]);
        $this->distribute(1000, 4)->shouldReturn([250, 250, 250, 250]);
        $this->distribute(-1000, 4)->shouldReturn([-250, -250, -250, -250]);
    }

    function it_distributes_integers_that_cannot_be_split_equally()
    {
        $this->distribute(1000, 3)->shouldReturn([334, 333, 333]);
        $this->distribute(-1000, 3)->shouldReturn([-334, -333, -333]);
    }

    function it_throws_an_exception_if_number_of_targets_is_not_integer_or_below_1()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Number of targets must be an integer, bigger than 0.'))
            ->during('distribute', [1000, 'test'])
        ;
        $this
            ->shouldThrow(new \InvalidArgumentException('Number of targets must be an integer, bigger than 0.'))
            ->during('distribute', [1000, 0])
        ;
    }
}
