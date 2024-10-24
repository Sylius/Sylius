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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ImageUploadListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $imageUploader): void
    {
        $this->beConstructedWith($imageUploader);
    }

    function it_uploads_image_of_image_aware_entity(
        ImageUploaderInterface $imageUploader,
        ImageAwareInterface $imageAware,
        ImageInterface $image,
        GenericEvent $event,
    ): void {
        $event->getSubject()->willReturn($imageAware);
        $imageAware->getImage()->willReturn($image);

        $imageUploader->upload($image)->shouldBeCalled();

        $this->uploadImage($event);
    }

    function it_throws_an_exception_if_event_subject_is_not_image_aware(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('uploadImage', [$event])
        ;
    }
}
