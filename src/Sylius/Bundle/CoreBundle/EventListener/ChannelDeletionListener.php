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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class ChannelDeletionListener
{
    public function __construct(private ChannelDeletionCheckerInterface $channelDeletionChecker)
    {
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
                ChannelInterface::class,
            );
        }

        if (!$this->channelDeletionChecker->isDeletable($channel)) {
            $event->stop('sylius.channel.delete_error');
        }
    }
}
