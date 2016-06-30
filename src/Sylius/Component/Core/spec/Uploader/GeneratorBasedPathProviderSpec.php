<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Uploader\GeneratorBasedPathProvider;
use Sylius\Component\Core\Uploader\PathGeneratorInterface;
use Sylius\Component\Core\Uploader\PathProviderInterface;

/**
 * @mixin GeneratorBasedPathProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class GeneratorBasedPathProviderSpec extends ObjectBehavior
{
    function let(PathGeneratorInterface $pathGenerator)
    {
        $this->beConstructedWith($pathGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Uploader\GeneratorBasedPathProvider');
    }

    function it_implements_path_provider_interface()
    {
        $this->shouldImplement(PathProviderInterface::class);
    }

    function it_returns_first_unique_path_that_was_generated(
        PathGeneratorInterface $pathGenerator,
        Filesystem $filesystem,
        \SplFileInfo $file
    ) {
        $pathGenerator->generate($file)->willReturn(new \ArrayIterator([
            'non/unique/path',
            'first/unique/path',
            'second/unique/path',
        ]));

        $filesystem->has('non/unique/path')->willReturn(true);
        $filesystem->has('first/unique/path')->willReturn(false);
        $filesystem->has('second/unique/path')->willReturn(false);

        $this->provide($filesystem, $file)->shouldReturn('first/unique/path');
    }

    function it_throws_an_exception_if_generator_is_empty(
        PathGeneratorInterface $pathGenerator,
        Filesystem $filesystem,
        \SplFileInfo $file
    ) {
        $pathGenerator->generate($file)->willReturn(new \ArrayIterator([]));

        $this->shouldThrow(\RuntimeException::class)->during('provide', [$filesystem, $file]);
    }

    function it_throws_an_exception_if_generation_cannot_be_done(
        PathGeneratorInterface $pathGenerator,
        Filesystem $filesystem,
        \SplFileInfo $file
    ) {
        $pathGenerator->generate($file)->willReturn(new \ArrayIterator(['non/unique/path1', 'non/unique/path2']));

        $filesystem->has('non/unique/path1')->willReturn(true);
        $filesystem->has('non/unique/path2')->willReturn(true);

        $this->shouldThrow(\RuntimeException::class)->during('provide', [$filesystem, $file]);
    }
}
