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
use Sylius\Component\Core\Uploader\ChunkingPathGenerator;
use Sylius\Component\Core\Uploader\PathGeneratorInterface;

/**
 * @mixin ChunkingPathGenerator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChunkingPathGeneratorSpec extends ObjectBehavior
{
    function let(PathGeneratorInterface $decoratedPathGenerator)
    {
        $numberOfChunks = 3;
        $chunkLength = 2;

        $this->beConstructedWith($decoratedPathGenerator, $numberOfChunks, $chunkLength);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Uploader\ChunkingPathGenerator');
    }

    function it_implements_path_generator_interface()
    {
        $this->shouldImplement(PathGeneratorInterface::class);
    }

    function it_chunks_a_path(PathGeneratorInterface $decoratedPathGenerator, \SplFileInfo $file)
    {
        $decoratedPathGenerator->generate($file)->willReturn(new \ArrayIterator(['deadbeef.jpg']));

        $this->generate($file)->shouldGenerate(['de/ad/be/ef.jpg']);
    }
}
