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

final class NoopThemeHierarchyProviderSpec extends ObjectBehavior
{
    function it_implements_theme_hierarchy_provider_interface(): void
    {
        $this->shouldImplement(ThemeHierarchyProviderInterface::class);
    }

    function it_returns_array_with_given_theme_as_only_element(ThemeInterface $theme): void
    {
        $this->getThemeHierarchy($theme)->shouldReturn([$theme]);
    }
}
