<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ThemeBundle\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ThemeBundle\Context\SettableThemeContext;
use Sylius\ThemeBundle\Context\ThemeContextInterface;
use Sylius\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin SettableThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SettableThemeContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ThemeBundle\Context\SettableThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_has_theme(ThemeInterface $theme)
    {
        $this->getTheme()->shouldReturn(null);

        $this->setTheme($theme);
        $this->getTheme()->shouldReturn($theme);
    }
}
