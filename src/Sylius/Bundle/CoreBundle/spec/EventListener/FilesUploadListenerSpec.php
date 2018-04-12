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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\FileInterface;
use Sylius\Component\Core\Model\FilesAwareInterface;
use Sylius\Component\Core\Uploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class FilesUploadListenerSpec extends ObjectBehavior
{
    function let(FileUploaderInterface $uploader): void
    {
        $this->beConstructedWith($uploader);
    }

    function it_uses_file_uploader_to_upload_files(
        GenericEvent $event,
        FilesAwareInterface $subject,
        FileInterface $file,
        FileUploaderInterface $uploader
    ): void {
        $event->getSubject()->willReturn($subject);
        $subject->getFiles()->willReturn(new ArrayCollection([$file->getWrappedObject()]));
        $file->hasFile()->willReturn(true);
        $file->getPath()->willReturn('some_path');
        $uploader->upload($file)->shouldBeCalled();

        $this->uploadFiles($event);
    }

    function it_throws_exception_if_event_subject_is_not_an_file_aware(
        GenericEvent $event,
        \stdClass $object
    ): void {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadFiles($event)
        ;
    }
}
