<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Asset\Package;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Asset\Package\PathPackage;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PathPackageSpec extends ObjectBehavior
{
    function let(
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $pathResolver
    ) {
        $this->beConstructedWith('/', $versionStrategy, $themeContext, $pathResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PathPackage::class);
    }

    function it_implements_package_interface_interface()
    {
        $this->shouldImplement(PackageInterface::class);
    }

    function it_returns_vanilla_url_if_there_are_no_active_themes(ThemeContextInterface $themeContext, VersionStrategyInterface $versionStrategy)
    {
        $path = 'bundles/sample/asset.js';

        $themeContext->getTheme()->shouldBeCalled()->willReturn(null);
        $versionStrategy->applyVersion($path)->shouldBeCalled()->willReturn($path);

        $this->getUrl($path)->shouldReturn('/'.$path);
    }

    function it_returns_modified_url_if_there_is_active_theme(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $theme
    ) {
        $path = 'bundles/sample/asset.js';

        $themeAssetPath = 'bundles/theme/foo/bar/sample/asset.js';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $pathResolver->resolve($path, $theme)->shouldBeCalled()->willReturn($themeAssetPath);
        $versionStrategy->applyVersion($themeAssetPath)->shouldBeCalled()->willReturn($themeAssetPath);

        $this->getUrl($path)->shouldReturn('/'.$themeAssetPath);
    }
}
