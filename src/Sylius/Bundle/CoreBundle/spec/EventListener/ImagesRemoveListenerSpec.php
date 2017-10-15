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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\ImagesRemoveListener;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

final class ImagesRemoveListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $imageUploader, CacheManager $cacheManager, FilterManager $filterManager): void
    {
        $this->beConstructedWith($imageUploader, $cacheManager, $filterManager);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ImagesRemoveListener::class);
    }

    function it_removes_file_on_post_remove_event(
        ImageUploaderInterface $imageUploader,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        LifecycleEventArgs $event,
        ImageInterface $image,
        FilterConfiguration $filterConfiguration
    ): void {
        $event->getEntity()->willReturn($image);
        $image->getPath()->willReturn('image/path');

        $filterManager->getFilterConfiguration()->willReturn($filterConfiguration);
        $filterConfiguration->all()->willReturn(['sylius_small' => 'thumbnalis']);
        $imageUploader->remove('image/path')->shouldBeCalled();
        $cacheManager->remove('image/path', ['sylius_small'])->shouldBeCalled();

        $this->postRemove($event);
    }

    function it_does_nothing_if_entity_is_not_image(
        ImageUploaderInterface $imageUploader,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn(new \stdClass());
        $filterManager->getFilterConfiguration()->shouldNotBeCalled();
        $imageUploader->remove(Argument::any())->shouldNotBeCalled();
        $cacheManager->remove(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->postRemove($event);
    }
}
