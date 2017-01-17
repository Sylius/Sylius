<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
final class ChannelDeletionListener
{
    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @param ChannelRepository $repository
     */
    public function __construct(ChannelRepository $repository)
    {
        $this->channelRepository = $repository;
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     *
     * @param ResourceControllerEvent $event
     */
    public function onChannelPreDelete(ResourceControllerEvent $event)
    {
        $channel = $event->getSubject();

        if (!$channel instanceof ChannelInterface) {
            throw new UnexpectedTypeException(
                $channel,
                ChannelInterface::class
            );
        }

        $results = $this->channelRepository->findBy(['enabled' => true]);

        if (!$results || (count($results) === 1 && current($results) === $channel)) {
            $event->stop('sylius.channel.delete_error');
        }
    }
}
