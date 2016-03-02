<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MediaBundle\Form\EventSubscriber;

use Doctrine\ODM\PHPCR\DocumentManager;
use Sylius\Component\Media\Model\Image;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class SyncImageMediaIdSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(
        DocumentManager $documentManager
    ) {
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        /** @var Image $data */
        $data = $event->getData();
        $media = $data->getMedia();

        if (null === $media) {
            return;
        }

        if (null === $media->getId()) {
            $this->documentManager->persist($media);
            $this->documentManager->flush();
        }

        if ($media->getId() !== $data->getMediaId()) {
            // This actually helps trigger preUpdate doctrine event since
            // doctrine is not tracking changes on $media field of Image entity.
            //
            // Here we forcefully update $mediaId (which is tracked by doctrine) to trigger
            // a change when a new media has been uploaded/selected.
            $data->setMediaId($media->getId());
        }
    }
}
