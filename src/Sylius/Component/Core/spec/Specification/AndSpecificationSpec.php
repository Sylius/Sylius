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
use Sylius\Component\Core\Specification\MemorySpecification;
use Sylius\Component\Core\Specification\Specification;

class AndSpecificationSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            new MemorySpecification(true),
            new MemorySpecification(true),
        );
    }

    function it_implements_specification(): void
    {
        $this->shouldImplement(Specification::class);
    }

    function it_is_satisfied_by_object(): void
    {
        $this->isSatisfiedBy(new class(){})->shouldBe(true);
    }

    function it_is_not_satisfied_by_object(): void
    {
        $this->beConstructedWith(
            new MemorySpecification(true),
            new MemorySpecification(false),
        );
        $this->isSatisfiedBy(new class(){})->shouldBe(false);
    }
}
