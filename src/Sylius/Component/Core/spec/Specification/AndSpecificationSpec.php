<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Specification;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Specification\Specification;

class AndSpecificationSpec extends ObjectBehavior
{
    function let(Specification $left, Specification $right): void
    {
        $this->beConstructedWith($left, $right);
    }

    function it_implements_specification(): void
    {
        $this->shouldImplement(Specification::class);
    }

    function it_is_satisfied_by_object_when_left_and_right_are_true(Specification $left, Specification $right, $candidate): void
    {
        $left->isSatisfiedBy($candidate)->willReturn(true);
        $right->isSatisfiedBy($candidate)->willReturn(true);

        $this->isSatisfiedBy($candidate)->shouldBe(true);
    }

    function it_is_not_satisfied_by_object_when_right_is_false(Specification $left, Specification $right, $candidate): void
    {
        $left->isSatisfiedBy($candidate)->willReturn(true);
        $right->isSatisfiedBy($candidate)->willReturn(false);

        $this->isSatisfiedBy($candidate)->shouldBe(false);
    }

    function it_is_not_satisfied_by_object_when_left_is_false(Specification $left, Specification $right, $candidate): void
    {
        $left->isSatisfiedBy($candidate)->willReturn(false);
        $right->isSatisfiedBy($candidate)->willReturn(true);

        $this->isSatisfiedBy($candidate)->shouldBe(false);
    }

    function it_is_not_satisfied_by_object_when_both_are_false(Specification $left, Specification $right, $candidate): void
    {
        $left->isSatisfiedBy($candidate)->willReturn(false);
        $right->isSatisfiedBy($candidate)->willReturn(false);

        $this->isSatisfiedBy($candidate)->shouldBe(false);
    }
}
