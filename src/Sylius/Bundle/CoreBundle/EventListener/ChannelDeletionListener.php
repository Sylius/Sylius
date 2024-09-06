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

use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class ChannelDeletionListener
{
    public function __construct(private ChannelDeletionCheckerInterface $channelDeletionChecker)
    {
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     */
    public function onChannelPreDelete(GenericEvent $event): void
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
