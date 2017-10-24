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

namespace spec\Sylius\Bundle\ThemeBundle\Asset;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class PathResolverSpec extends ObjectBehavior
{
    function it_implements_path_resolver_interface(): void
    {
        $this->shouldImplement(PathResolverInterface::class);
    }

    function it_returns_modified_path_if_its_referencing_bundle_asset(ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');

        $this->resolve('bundles/asset.min.js', $theme)->shouldReturn('bundles/_themes/theme/name/asset.min.js');
    }

    function it_does_not_change_path_if_its_not_referencing_bundle_asset(ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');

        $this->resolve('/long.path/asset.min.js', $theme)->shouldReturn('/long.path/asset.min.js');
    }
}
