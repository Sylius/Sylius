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

namespace spec\Sylius\Bundle\ThemeBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

final class EmptyThemeContextSpec extends ObjectBehavior
{
    function it_implements_theme_context_interface(): void
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_always_returns_null(): void
    {
        $this->getTheme()->shouldReturn(null);
    }
}
