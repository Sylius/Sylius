<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ImagesUploadListener;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ImagesUploadListenerTest extends TestCase
{
    private ImageUploaderInterface&MockObject $uploader;

    private ImagesUploadListener $listener;

    private GenericEvent&MockObject $event;

    private ImagesAwareInterface&MockObject $subject;

    private ImageInterface&MockObject $image;

    protected function setUp(): void
    {
        $this->uploader = $this->createMock(ImageUploaderInterface::class);
        $this->event = $this->createMock(GenericEvent::class);
        $this->subject = $this->createMock(ImagesAwareInterface::class);
        $this->image = $this->createMock(ImageInterface::class);
        $this->listener = new ImagesUploadListener($this->uploader);
    }

    public function testItUsesImageUploaderToUploadImages(): void
    {
        $this->event->method('getSubject')->willReturn($this->subject);
        $this->subject->method('getImages')->willReturn(new ArrayCollection([$this->image]));
        $this->image->method('hasFile')->willReturn(true);
        $this->image->method('getPath')->willReturn('some_path');

        $this->uploader->expects($this->once())->method('upload')->with($this->image);

        $this->listener->uploadImages($this->event);
    }

    public function testItThrowsExceptionIfEventSubjectIsNotImageAware(): void
    {
        $nonImageAwareObject = new \stdClass();

        $this->event->method('getSubject')->willReturn($nonImageAwareObject);

        $this->expectException(\InvalidArgumentException::class);

        $this->listener->uploadImages($this->event);
    }
}
