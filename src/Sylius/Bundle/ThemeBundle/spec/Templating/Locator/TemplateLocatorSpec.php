<?php

namespace spec\Sylius\Bundle\ThemeBundle\Templating\Locator;

use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @mixin TemplateLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TemplateLocatorSpec extends FixtureAwareObjectBehavior
{
    function let(KernelInterface $kernel, ThemeRepositoryInterface $themeRepository, ThemeContextInterface $themeContext)
    {
        $this->beConstructedWith($kernel, $themeRepository, $themeContext, $this->getFixturePath('app'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocator');
    }

    function it_implements_file_locator_interface()
    {
        $this->shouldImplement('Symfony\Component\Config\FileLocatorInterface');
    }

    /**
     * BundleResources & AppResources: every path, considering themes order
     */
}