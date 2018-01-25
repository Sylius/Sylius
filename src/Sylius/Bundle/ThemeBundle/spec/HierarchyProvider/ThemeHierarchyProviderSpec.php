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

namespace spec\Sylius\Bundle\ThemeBundle\HierarchyProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class ThemeHierarchyProviderSpec extends ObjectBehavior
{
    function it_implements_theme_hierarchy_provider_interface(): void
    {
        $this->shouldImplement(ThemeHierarchyProviderInterface::class);
    }

    function it_returns_theme_list_in_hierarchized_order(ThemeInterface $firstTheme, ThemeInterface $secondTheme): void
    {
        $firstTheme->getParents()->willReturn([$secondTheme]);
        $secondTheme->getParents()->willReturn([]);

        $this->getThemeHierarchy($firstTheme)->shouldReturn([$firstTheme, $secondTheme]);
    }
}
