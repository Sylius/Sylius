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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Uploader\NonrandomPathGenerator;
use Sylius\Component\Core\Uploader\PathGeneratorInterface;

/**
 * @mixin NonrandomPathGenerator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class NonrandomPathGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NonrandomPathGenerator::class);
    }

    function it_implements_Sylius_uploader_path_generator_interface()
    {
        $this->shouldImplement(PathGeneratorInterface::class);
    }

    function it_creates_unique_nonexistent_path_for_file(\SplFileInfo $file)
    {
        $file->getFilename()->willReturn('FILENAME');
        $file->getMTime()->willReturn('MTIME');
        $file->getExtension()->willReturn('jpg');

        $firstGeneratedPath = md5('FILENAME.MTIME.0') . '.jpg';
        $secondGeneratedPath = md5('FILENAME.MTIME.1') . '.jpg';

        $this->generate($file)->shouldGenerate([$firstGeneratedPath, $secondGeneratedPath]);
    }
}
