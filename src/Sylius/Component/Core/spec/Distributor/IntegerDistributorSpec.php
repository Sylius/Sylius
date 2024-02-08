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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;

final class IntegerDistributorSpec extends ObjectBehavior
{
    function it_implements_an_integer_distributor_interface(): void
    {
        $this->shouldImplement(IntegerDistributorInterface::class);
    }

    function it_distributes_simple_integers(): void
    {
        $this->distribute(0, 4)->shouldReturn([0, 0, 0, 0]);
        $this->distribute(1000, 4)->shouldReturn([250, 250, 250, 250]);
        $this->distribute(-1000, 4)->shouldReturn([-250, -250, -250, -250]);
    }

    function it_distributes_integers_that_cannot_be_split_equally(): void
    {
        $this->distribute(1000, 3)->shouldReturn([334, 333, 333]);
        $this->distribute(-1000, 3)->shouldReturn([-334, -333, -333]);
    }

    function it_throws_an_exception_if_number_of_targets_is_below_1(): void
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Number of targets must be bigger than 0.'))
            ->during('distribute', [1000, 0])
        ;
    }
}
