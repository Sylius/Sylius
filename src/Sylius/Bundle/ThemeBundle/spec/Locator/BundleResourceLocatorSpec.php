<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\BundleResourceLocator;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class BundleResourceLocatorSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, KernelInterface $kernel)
    {
        $this->beConstructedWith($filesystem, $kernel);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BundleResourceLocator::class);
    }

    function it_implements_resource_locator_interface()
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_locates_bundle_resource(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeInterface $theme,
        BundleInterface $childBundle,
        BundleInterface $parentBundle
    ) {
        $kernel->getBundle('ParentBundle', false)->willReturn([$childBundle, $parentBundle]);

        $childBundle->getName()->willReturn('ChildBundle');
        $parentBundle->getName()->willReturn('ParentBundle');

        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/ChildBundle/views/index.html.twig')->shouldBeCalled()->willReturn(false);
        $filesystem->exists('/theme/path/ParentBundle/views/index.html.twig')->shouldBeCalled()->willReturn(true);

        $this->locateResource('@ParentBundle/Resources/views/index.html.twig', $theme)->shouldReturn('/theme/path/ParentBundle/views/index.html.twig');
    }

    function it_throws_an_exception_if_resource_can_not_be_located(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeInterface $theme,
        BundleInterface $childBundle,
        BundleInterface $parentBundle
    ) {
        $kernel->getBundle('ParentBundle', false)->willReturn([$childBundle, $parentBundle]);

        $childBundle->getName()->willReturn('ChildBundle');
        $parentBundle->getName()->willReturn('ParentBundle');

        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/ChildBundle/views/index.html.twig')->shouldBeCalled()->willReturn(false);
        $filesystem->exists('/theme/path/ParentBundle/views/index.html.twig')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['@ParentBundle/Resources/views/index.html.twig', $theme]);
    }

    function it_throws_an_exception_if_resource_path_does_not_start_with_an_asperand(ThemeInterface $theme)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateResource', ['ParentBundle/Resources/views/index.html.twig', $theme]);
    }

    function it_throws_an_exception_if_resource_path_contains_two_dots_in_a_row(ThemeInterface $theme)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateResource', ['@ParentBundle/Resources/views/../views/index.html.twig', $theme]);
    }

    function it_throws_an_exception_if_resource_path_does_not_contain_resources_dir(ThemeInterface $theme)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateResource', ['@ParentBundle/views/Resources.index.html.twig', $theme]);
    }
}
