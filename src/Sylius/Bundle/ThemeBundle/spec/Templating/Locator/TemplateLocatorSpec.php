<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Locator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @mixin TemplateLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplateLocatorSpec extends ObjectBehavior
{
    function let(
        FileLocatorInterface $templateLocator,
        ThemeContextInterface $themeContext,
        ResourceLocatorInterface $resourceLocator
    ) {
        $this->beConstructedWith($templateLocator, $themeContext, $resourceLocator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocator');
    }

    function it_implements_file_locator_interface()
    {
        $this->shouldImplement(FileLocatorInterface::class);
    }

    function it_throws_an_exception_if_located_thing_is_not_an_instance_of_template_reference_interface()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locate', ['not an instance']);
    }

    function it_returns_first_possible_theme_resource(
        ThemeContextInterface $themeContext,
        ResourceLocatorInterface $resourceLocator,
        TemplateReferenceInterface $template,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $template->getPath()->willReturn('template/path');

        $themeContext->getThemeHierarchy()->willReturn([$firstTheme, $secondTheme]);

        $resourceLocator->locateResource('template/path', $firstTheme)->willThrow(ResourceNotFoundException::class);
        $resourceLocator->locateResource('template/path', $secondTheme)->willReturn('/second/theme/template/path');
        
        $this->locate($template)->shouldReturn('/second/theme/template/path');
    }

    function it_falls_back_to_decorated_template_locator_if_themed_tempaltes_can_not_be_found(
        FileLocatorInterface $templateLocator,
        ThemeContextInterface $themeContext,
        ResourceLocatorInterface $resourceLocator,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getPath()->willReturn('template/path');

        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $resourceLocator->locateResource('template/path', $theme)->willThrow(ResourceNotFoundException::class);

        $templateLocator->locate($template, Argument::cetera())->willReturn('/app/template/path');

        $this->locate($template)->shouldReturn('/app/template/path');
    }

    function it_falls_back_to_decorated_template_locator_if_there_are_no_themes_active(
        FileLocatorInterface $templateLocator,
        ThemeContextInterface $themeContext,
        TemplateReferenceInterface $template
    ) {
        $template->getPath()->willReturn('template/path');

        $themeContext->getThemeHierarchy()->willReturn([]);

        $templateLocator->locate($template, Argument::cetera())->willReturn('/app/template/path');

        $this->locate($template)->shouldReturn('/app/template/path');
    }
}
