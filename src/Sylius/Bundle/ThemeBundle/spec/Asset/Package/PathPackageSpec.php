<?php

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

    function it_returns_vanilla_url_if_there_are_no_themes(ThemeContextInterface $themeContext, VersionStrategyInterface $versionStrategy)
    {
        $path = 'bundles/sample/asset.js';

        $themeContext->getThemesSortedByPriorityInDescendingOrder()->shouldBeCalled()->willReturn([]);
        $versionStrategy->applyVersion($path)->shouldBeCalled()->willReturn($path);

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $path);
    }

    function it_returns_vanilla_url_if_asset_does_not_exist_in_any_theme(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $theme
    ) {
        $path = 'bundles/sample/asset_not_included_in_any_themes.js';

        $themeContext->getThemesSortedByPriorityInDescendingOrder()->shouldBeCalled()->willReturn([$theme]);
        $pathResolver->resolve($path, $theme)->shouldBeCalled()->willReturn('/this/file/does/not/exist');
        $versionStrategy->applyVersion($path)->shouldBeCalled()->willReturn($path);

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $path);
    }

    function it_returns_modified_url_if_asset_exists_in_given_themes(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $firstTheme, ThemeInterface $secondTheme
    ) {
        $path = 'bundles/sample/asset.js';

        $firstThemeAssetPath = 'bundles/sample/asset_sample_theme.js';

        $themeContext->getThemesSortedByPriorityInDescendingOrder()->shouldBeCalled()->willReturn([$firstTheme, $secondTheme]);
        $pathResolver->resolve($path, $firstTheme)->shouldBeCalled()->willReturn($firstThemeAssetPath);
        $versionStrategy->applyVersion($firstThemeAssetPath)->shouldBeCalled()->willReturn($firstThemeAssetPath);

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $firstThemeAssetPath);
    }

    function it_returns_modified_url_if_asset_exists_in_one_of_given_themes(
        ThemeContextInterface $themeContext,
        VersionStrategyInterface $versionStrategy,
        PathResolverInterface $pathResolver,
        ThemeInterface $firstTheme, ThemeInterface $secondTheme
    ) {
        $path = 'bundles/sample/asset2.js';

        $firstThemeAssetPath = 'bundles/sample/asset2_sample_theme.js';
        $secondThemeAssetPath = 'bundles/sample/asset2_second_sample_theme.js';

        $themeContext->getThemesSortedByPriorityInDescendingOrder()->shouldBeCalled()->willReturn([$firstTheme, $secondTheme]);
        $pathResolver->resolve($path, $firstTheme)->shouldBeCalled()->willReturn($firstThemeAssetPath);
        $pathResolver->resolve($path, $secondTheme)->shouldBeCalled()->willReturn($secondThemeAssetPath);
        $versionStrategy->applyVersion($secondThemeAssetPath)->shouldBeCalled()->willReturn($secondThemeAssetPath);

        $this->getUrl($path)->shouldReturn($this->getBasePath() . $secondThemeAssetPath);
    }

    /**
     * @return string
     */
    private function getBasePath()
    {
        return $this->getFixturePath('web') . '/';
    }
}