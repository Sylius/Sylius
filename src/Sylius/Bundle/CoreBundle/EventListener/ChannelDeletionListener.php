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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class ChannelDeletionListener
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $repository)
    {
        $this->channelRepository = $repository;
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     */
    public function onChannelPreDelete(ResourceControllerEvent $event): void
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
