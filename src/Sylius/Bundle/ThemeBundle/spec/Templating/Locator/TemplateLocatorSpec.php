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
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocator;
use Sylius\Bundle\ThemeBundle\Templating\Locator\TemplateLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplateLocatorSpec extends ObjectBehavior
{
    function let(ResourceLocatorInterface $resourceLocator)
    {
        $this->beConstructedWith($resourceLocator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TemplateLocator::class);
    }

    function it_implements_template_locator_interface()
    {
        $this->shouldImplement(TemplateLocatorInterface::class);
    }

    function it_proxies_locating_template_to_resource_locator(
        ResourceLocatorInterface $resourceLocator,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getPath()->willReturn('@AcmeBundle/Resources/views/index.html.twig');

        $resourceLocator->locateResource('@AcmeBundle/Resources/views/index.html.twig', $theme)->willReturn('/acme/index.html.twig');

        $this->locateTemplate($template, $theme)->shouldReturn('/acme/index.html.twig');
    }

    function it_does_not_catch_exceptions_thrown_while_locating_template_to_resource_locator_even(
        ResourceLocatorInterface $resourceLocator,
        TemplateReferenceInterface $template,
        ThemeInterface $theme
    ) {
        $template->getPath()->willReturn('@AcmeBundle/Resources/views/index.html.twig');

        $resourceLocator->locateResource('@AcmeBundle/Resources/views/index.html.twig', $theme)->willThrow(ResourceNotFoundException::class);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateTemplate', [$template, $theme]);
    }
}
