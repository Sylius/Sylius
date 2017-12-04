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
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

final class UrlPackageSpec extends ObjectBehavior
{
    function let(
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $urlResolver
    ): void {
        $this->beConstructedWith(['https://cdn-url.com/'], $versionStrategy, $themeContext, $urlResolver);
    }

    function it_implements_package_interface(): void
    {
        $this->shouldImplement(PackageInterface::class);
    }

    function it_extends_symfony_url_package(): void
    {
        $this->shouldImplement(UrlPackage::class);
    }

    function it_returns_vanilla_url_if_there_are_no_active_themes_and_with_base_url(
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $urlResolver
    ): void {
        $this->beConstructedWith('https://cdn-url.com/', $versionStrategy, $themeContext, $urlResolver);

        $url = 'bundles/sample/asset.js';
        $versionedPath = 'bundles/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn(null);
        $versionStrategy->applyVersion($url)->shouldBeCalled()->willReturn($versionedPath);

        $this->getUrl($url)->shouldReturn('https://cdn-url.com/' . $versionedPath);
    }

    function it_returns_modified_url_if_there_is_active_theme_and_with_base_url(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $urlResolver,
        ThemeInterface $theme
    ): void {
        $url = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = 'bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $urlResolver->resolve($url, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($url)->shouldReturn('https://cdn-url.com/' . $versionedThemedPath);
    }

    function it_returns_url_without_changes_if_it_is_absolute(): void
    {
        $this->getUrl('//localhost/asset.js')->shouldReturn('//localhost/asset.js');
        $this->getUrl('https://localhost/asset.js')->shouldReturn('https://localhost/asset.js');
    }

    function it_does_prepend_it_with_base_url_if_modified_url_is_an_absolute_one(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $urlResolver,
        ThemeInterface $theme
    ): void {
        $url = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = 'https://cdn-url.com/bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $urlResolver->resolve($url, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($url)->shouldReturn($versionedThemedPath);
    }

    function it_does_not_prepend_it_with_base_url_if_modified_url_is_an_absolute_url(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $urlResolver,
        ThemeInterface $theme
    ): void {
        $url = 'bundles/sample/asset.js';
        $themedPath = 'bundles/theme/foo/bar/sample/asset.js';
        $versionedThemedPath = 'https://bundles/theme/foo/bar/sample/asset.js?v=42';

        $themeContext->getTheme()->shouldBeCalled()->willReturn($theme);
        $urlResolver->resolve($url, $theme)->shouldBeCalled()->willReturn($themedPath);
        $versionStrategy->applyVersion($themedPath)->shouldBeCalled()->willReturn($versionedThemedPath);

        $this->getUrl($url)->shouldReturn($versionedThemedPath);
    }
}
