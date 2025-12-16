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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ImageUploadListenerTest extends TestCase
{
    private ImageUploaderInterface&MockObject $imageUploader;

    private ImageUploadListener $listener;

    private ImageAwareInterface&MockObject $imageAware;

    private ImageInterface&MockObject $image;

    private GenericEvent&MockObject $event;

    protected function setUp(): void
    {
        $this->imageUploader = $this->createMock(ImageUploaderInterface::class);
        $this->listener = new ImageUploadListener($this->imageUploader);
        $this->imageAware = $this->createMock(ImageAwareInterface::class);
        $this->image = $this->createMock(ImageInterface::class);
        $this->event = $this->createMock(GenericEvent::class);
    }

    public function testItUploadsImageOfImageAwareEntity(): void
    {
        $this->event->method('getSubject')->willReturn($this->imageAware);
        $this->imageAware->method('getImage')->willReturn($this->image);

        $this->imageUploader->expects($this->once())->method('upload')->with($this->image);

        $this->listener->uploadImage($this->event);
    }

    public function testItThrowsExceptionIfEventSubjectIsNotImageAware(): void
    {
        $this->event->method('getSubject')->willReturn('badObject');

        $this->expectException(\InvalidArgumentException::class);

        $this->listener->uploadImage($this->event);
    }
}
