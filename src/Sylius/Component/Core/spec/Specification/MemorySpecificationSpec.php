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

class MemorySpecificationSpec extends ObjectBehavior
{
    function it_is_false(): void
    {
        $this->beConstructedWith(false);
        $this->isSatisfiedBy(new class(){})->shouldBe(false);
    }

    function it_is_true(): void
    {
        $this->beConstructedWith(true);
        $this->isSatisfiedBy(new class(){})->shouldBe(true);
    }
}
