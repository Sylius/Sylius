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
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ImagesUploadListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $uploader): void
    {
        $this->beConstructedWith($uploader);
    }

    function it_uses_image_uploader_to_upload_images(
        GenericEvent $event,
        ImagesAwareInterface $subject,
        ImageInterface $image,
        ImageUploaderInterface $uploader
    ): void {
        $event->getSubject()->willReturn($subject);
        $subject->getImages()->willReturn(new ArrayCollection([$image->getWrappedObject()]));
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('some_path');
        $uploader->upload($image)->shouldBeCalled();

        $this->uploadImages($event);
    }

    function it_throws_exception_if_event_subject_is_not_an_image_aware(
        GenericEvent $event,
        \stdClass $object
    ): void {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadImages($event)
        ;
    }
}
