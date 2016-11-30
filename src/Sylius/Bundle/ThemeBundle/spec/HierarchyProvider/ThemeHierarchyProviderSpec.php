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
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProvider;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeHierarchyProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeHierarchyProvider::class);
    }

    function it_implements_theme_hierarchy_provider_interface()
    {
        $this->shouldImplement(ThemeHierarchyProviderInterface::class);
    }

    function it_returns_theme_list_in_hierarchized_order(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getParents()->willReturn([$secondTheme]);
        $secondTheme->getParents()->willReturn([]);

        $this->getThemeHierarchy($firstTheme)->shouldReturn([$firstTheme, $secondTheme]);
    }
}
