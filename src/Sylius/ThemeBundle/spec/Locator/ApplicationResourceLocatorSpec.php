<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\ThemeBundle\Locator\ApplicationResourceLocator;
use Sylius\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @mixin ApplicationResourceLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ApplicationResourceLocatorSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ThemeBundle\Locator\ApplicationResourceLocator');
    }

    function it_implements_resource_locator_interface()
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_locates_application_resource(Filesystem $filesystem, ThemeInterface $theme)
    {
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(true);

        $this->locateResource('resource', $theme)->shouldReturn('/theme/path/resource');
    }

    function it_throws_an_exception_if_resource_can_not_be_located(Filesystem $filesystem, ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['resource', $theme]);
    }
}
