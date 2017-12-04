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

namespace spec\Sylius\Bundle\ThemeBundle\Asset\Package;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

final class PathPackageSpec extends ObjectBehavior
{
    function let(
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $pathResolver
    ): void {
        $this->beConstructedWith('/', $versionStrategy, $themeContext, $pathResolver);
    }

    function it_implements_package_interface_interface(): void
    {
        $this->shouldImplement(PackageInterface::class);
    }

    function it_returns_vanilla_path_if_there_are_no_active_themes(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy
    ): void {
        $path = 'bundles/sample/asset.js';
        $versionedPath = 'bundles/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn(null);
        $versionStrategy->applyVersion($path)->shouldBeCalled()->willReturn($versionedPath);

        $this->getUrl($path)->shouldReturn('/' . $versionedPath);
    }

    function it_returns_modified_path_if_there_is_active_theme(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $theme
    ): void {
        $path = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = 'bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $pathResolver->resolve($path, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($path)->shouldReturn('/' . $versionedThemedPath);
    }

    function it_returns_path_without_changes_if_it_is_absolute(): void
    {
        $this->getUrl('//localhost/asset.js')->shouldReturn('//localhost/asset.js');
        $this->getUrl('https://localhost/asset.js')->shouldReturn('https://localhost/asset.js');
    }

    function it_does_not_prepend_it_with_base_path_if_modified_path_is_an_absolute_one(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $theme
    ): void {
        $path = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = '/bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $pathResolver->resolve($path, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($path)->shouldReturn($versionedThemedPath);
    }

    function it_does_not_prepend_it_with_base_path_if_modified_path_is_an_absolute_url(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $theme
    ): void {
        $path = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = 'https://bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $pathResolver->resolve($path, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($path)->shouldReturn($versionedThemedPath);
    }
}
