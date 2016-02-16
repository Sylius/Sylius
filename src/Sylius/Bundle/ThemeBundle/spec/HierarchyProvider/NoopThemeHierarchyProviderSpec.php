<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\HierarchyProvider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\NoopThemeHierarchyProvider;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin NoopThemeHierarchyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class NoopThemeHierarchyProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\HierarchyProvider\NoopThemeHierarchyProvider');
    }

    function it_implements_theme_hierarchy_provider_interface()
    {
        $this->shouldImplement(ThemeHierarchyProviderInterface::class);
    }

    function it_returns_array_with_given_theme_as_only_element(ThemeInterface $theme)
    {
        $this->getThemeHierarchy($theme)->shouldReturn([$theme]);
    }

    function it_returns_empty_array_if_given_theme_is_null()
    {
        $this->getThemeHierarchy(null)->shouldReturn([]);
    }
}
