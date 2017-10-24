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

namespace spec\Sylius\Bundle\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Symfony\Component\Finder\Finder;

final class FinderFactorySpec extends ObjectBehavior
{
    function it_implements_finder_factory_interface(): void
    {
        $this->shouldImplement(FinderFactoryInterface::class);
    }

    function it_creates_a_brand_new_finder(): void
    {
        $this->create()->shouldHaveType(Finder::class);
    }
}
