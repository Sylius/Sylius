<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin ImageUploadListener
 *
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ImageUploadListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $uploader)
    {
        $this->beConstructedWith($uploader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImageUploadListener::class);
    }

    function it_uses_image_uploader_to_upload_images(
        GenericEvent $event,
        ImageAwareInterface $subject,
        ImageInterface $image,
        ImageUploaderInterface $uploader
    ) {
        $event->getSubject()->willReturn($subject);
        $subject->getImages()->willReturn([$image]);
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('some_path');
        $uploader->upload($image)->shouldBeCalled();

        $this->uploadImage($event);
    }

    function it_throws_exception_if_event_subject_is_not_an_image_aware(
        GenericEvent $event,
        \stdClass $object
    ) {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadImage($event)
        ;
    }
}
