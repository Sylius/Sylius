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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\FileInterface;
use Sylius\Component\Core\Model\FilesAwareInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonSpec extends ObjectBehavior
{
    function it_is_a_taxon(): void
    {
        $this->shouldImplement(TaxonInterface::class);
    }

    function it_implements_an_file_aware_interface(): void
    {
        $this->shouldImplement(FilesAwareInterface::class);
    }

    function it_initializes_a_file_collection_by_default(): void
    {
        $this->getFiles()->shouldHaveType(Collection::class);
    }

    function it_adds_a_file(FileInterface $file): void
    {
        $this->addFile($file);
        $this->hasFiles()->shouldReturn(true);
        $this->hasFile($file)->shouldReturn(true);
    }

    function it_removes_a_file(FileInterface $file): void
    {
        $this->addFile($file);
        $this->removeFile($file);
        $this->hasFile($file)->shouldReturn(false);
    }

    function it_returns_files_by_type(FileInterface $file): void
    {
        $file->getType()->willReturn('thumbnail');
        $file->setOwner($this)->shouldBeCalled();

        $this->addFile($file);

        $this->getFilesByType('thumbnail')->shouldBeLike(new ArrayCollection([$file->getWrappedObject()]));
    }
}
