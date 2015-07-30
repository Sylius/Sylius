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

use Sylius\Bundle\ThemeBundle\Asset\Package\PathPackage;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * @mixin PathPackage
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PathPackageSpec extends FixtureAwareObjectBehavior
{
    function let(
        VersionStrategyInterface $versionStrategy,
        ThemeContextInterface $themeContext,
        PathResolverInterface $pathResolver
    ) {
        chdir($this->getBasePath());

        $this->beConstructedWith($this->getBasePath(), $versionStrategy, $themeContext, $pathResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Asset\Package\PathPackage');
    }

    function it_implements_package_interface_interface()
    {
        $this->shouldImplement('Symfony\Component\Asset\PackageInterface');
    }

    function it_returns_vanilla_url_if_there_are_no_active_themes(ThemeContextInterface $themeContext, VersionStrategyInterface $versionStrategy)
    {
        $path = 'bundles/sample/asset.js';

        $themeContext->getTheme()->shouldBeCalled()->willReturn(null);
        $versionStrategy->applyVersion($path)->shouldBeCalled()->willReturn($path);

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $path);
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

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $themeAssetPath);
    }

    /**
     * @return string
     */
    private function getBasePath()
    {
        return $this->getFixturePath('web') . '/';
    }
}