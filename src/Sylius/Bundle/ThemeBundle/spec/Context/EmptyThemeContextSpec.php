<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\EmptyThemeContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

/**
 * @mixin EmptyThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class EmptyThemeContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Context\EmptyThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_always_returns_null()
    {
        $this->getTheme()->shouldReturn(null);
    }
}
