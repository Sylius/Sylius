<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ThemeBundle\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\SettingsBundle\Model\SettingsInterface;
use Sylius\ThemeBundle\Context\ThemeContextInterface;
use Sylius\ThemeBundle\Model\ThemeInterface;
use Sylius\ThemeBundle\Settings\ThemeSettingsManagerInterface;
use Sylius\ThemeBundle\Twig\SettingsExtension;

/**
 * @mixin SettingsExtension
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SettingsExtensionSpec extends ObjectBehavior
{
    function let(ThemeContextInterface $themeContext, ThemeSettingsManagerInterface $themeSettingsManager)
    {
        $this->beConstructedWith($themeContext, $themeSettingsManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ThemeBundle\Twig\SettingsExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_returns_an_empty_array_if_there_is_no_current_theme_set(ThemeContextInterface $themeContext)
    {
        $themeContext->getTheme()->willReturn(null);

        $this->getThemeSettings()->shouldReturn([]);
    }

    function it_returns_loaded_theme_settings(
        ThemeContextInterface $themeContext,
        ThemeSettingsManagerInterface $themeSettingsManager,
        ThemeInterface $theme,
        SettingsInterface $settings
    ) {
        $themeContext->getTheme()->willReturn($theme);

        $themeSettingsManager->load($theme)->willReturn($settings);

        $this->getThemeSettings()->shouldReturn($settings);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_theme_settings');
    }
}
