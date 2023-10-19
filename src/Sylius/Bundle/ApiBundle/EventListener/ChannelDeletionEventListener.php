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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

/** @experimental */
final class ChannelDeletionEventListener
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function protectFromRemovalTheOnlyChannelInStore(ViewEvent $event): void
    {
        $channel = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$channel instanceof ChannelInterface || $method !== Request::METHOD_DELETE) {
            return;
        }

        $results = $this->channelRepository->findBy(['enabled' => true]);

        if (!$results || (count($results) === 1 && current($results) === $channel)) {
            throw new ChannelCannotBeRemoved('The channel cannot be deleted. At least one enabled channel is required.');
        }
    }
}
