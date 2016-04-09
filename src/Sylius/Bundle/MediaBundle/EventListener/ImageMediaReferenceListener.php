<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Media\Model\ImageInterface;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image as CmfImage;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class ImageMediaReferenceListener implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
            'postLoad',
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $image = $event->getEntity();

        if (!$image instanceof ImageInterface) {
            return;
        }

        $media = $image->getMedia();

        if (null === $media) {
            return;
        }

        $id = $media->getId();

        if (null === $id) {
            $manager = $this->managerRegistry->getManagerForClass(get_class($media));
            $manager->persist($media);
            $manager->flush();

            $id = $media->getId();
        }

        $image->setMediaId($id);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->prePersist($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $image = $event->getObject();

        if (!$image instanceof ImageInterface) {
            return;
        }

        if (null === $image->getMediaId()) {
            return;
        }

        $media = $this->managerRegistry
            ->getManagerForClass(CmfImage::class)
            ->find(null, $image->getMediaId());

        $image->setMedia($media);
    }
}
