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

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;

final class ThemeLoadingFailedExceptionSpec extends ObjectBehavior
{
    function it_is_a_domain_exception(): void
    {
        $this->shouldHaveType(\DomainException::class);
    }

    function it_is_a_logic_exception(): void
    {
        $this->shouldHaveType(\LogicException::class);
    }
}
