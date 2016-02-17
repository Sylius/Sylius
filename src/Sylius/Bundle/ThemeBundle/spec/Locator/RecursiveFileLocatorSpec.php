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
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\RecursiveFileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @mixin RecursiveFileLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RecursiveFileLocatorSpec extends ObjectBehavior
{
    function let(FinderFactoryInterface $finderFactory)
    {
        $this->beConstructedWith($finderFactory, ['/search/path/']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Locator\RecursiveFileLocator');
    }

    function it_implements_sylius_file_locator_interface()
    {
        $this->shouldImplement(FileLocatorInterface::class);
    }

    function it_searches_for_file(FinderFactoryInterface $finderFactory, Finder $finder, SplFileInfo $splFileInfo)
    {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in(['/search/path/'])->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            $splFileInfo->getWrappedObject(),
        ]));
        $finder->count()->willReturn(1);

        $splFileInfo->getPathname()->willReturn('/search/path/nested/readme.md');

        $this->locateFileNamed('readme.md')->shouldReturn('/search/path/nested/readme.md');
    }

    function it_searches_for_files(
        FinderFactoryInterface $finderFactory,
        Finder $finder,
        SplFileInfo $firstSplFileInfo,
        SplFileInfo $secondSplFileInfo
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in(['/search/path/'])->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator([
            $firstSplFileInfo->getWrappedObject(),
            $secondSplFileInfo->getWrappedObject(),
        ]));
        $finder->count()->willReturn(2);

        $firstSplFileInfo->getPathname()->willReturn('/search/path/nested1/readme.md');
        $secondSplFileInfo->getPathname()->willReturn('/search/path/nested2/readme.md');

        $this->locateFilesNamed('readme.md')->shouldReturn([
            '/search/path/nested1/readme.md',
            '/search/path/nested2/readme.md',
        ]);
    }

    function it_throws_an_exception_if_searching_for_file_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', [null]);
    }

    function it_throws_an_exception_if_searching_for_files_with_empty_name()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', [null]);
    }

    function it_throws_an_exception_if_there_is_no_file_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in(['/search/path/'])->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator());
        $finder->count()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFileNamed', ['readme.md']);
    }

    function it_throws_an_exception_if_there_is_there_are_not_any_files_that_matches_the_given_name(
        FinderFactoryInterface $finderFactory,
        Finder $finder
    ) {
        $finderFactory->create()->willReturn($finder);

        $finder->name('readme.md')->shouldBeCalled()->willReturn($finder);
        $finder->in(['/search/path/'])->shouldBeCalled()->willReturn($finder);
        $finder->ignoreUnreadableDirs()->shouldBeCalled()->willReturn($finder);
        $finder->files()->shouldBeCalled()->willReturn($finder);

        $finder->getIterator()->willReturn(new \ArrayIterator());
        $finder->count()->willReturn(0);

        $this->shouldThrow(\InvalidArgumentException::class)->during('locateFilesNamed', ['readme.md']);
    }
}
