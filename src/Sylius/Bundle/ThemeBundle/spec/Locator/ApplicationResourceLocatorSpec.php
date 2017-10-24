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

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ApplicationResourceLocatorSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_implements_resource_locator_interface(): void
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_locates_application_resource(Filesystem $filesystem, ThemeInterface $theme): void
    {
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(true);

        $this->locateResource('resource', $theme)->shouldReturn('/theme/path/resource');
    }

    function it_throws_an_exception_if_resource_can_not_be_located(Filesystem $filesystem, ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/resource')->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['resource', $theme]);
    }
}
