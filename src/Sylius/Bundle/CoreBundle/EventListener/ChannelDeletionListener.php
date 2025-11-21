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
use Sylius\Component\Core\ShippingMethod\Updater\ChannelAwareShippingMethodUpdaterInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final readonly class ChannelDeletionListener
{
    public function __construct(
        private ChannelDeletionCheckerInterface $channelDeletionChecker,
        private ChannelAwareShippingMethodUpdaterInterface $shippingMethodUpdater,
    ) {
    }

    /**
     * Prevent channel deletion if no more channels enabled.
     */
    public function onChannelPreDelete(GenericEvent $event): void
    {
        $channel = $this->getChannel($event);

        if (!$this->channelDeletionChecker->isDeletable($channel)) {
            $event->stop('sylius.channel.delete_error');
        }
    }

    public function removeChannelConfigurationFromShippingMethods(GenericEvent $event): void
    {
        $channel = $this->getChannel($event);

        $channelCode = $channel->getCode();
        if ($channelCode === null) {
            return;
        }

        $this->shippingMethodUpdater->removeChannelConfigurationFromShippingMethods($channelCode);
    }

    private function getChannel(GenericEvent $event): ChannelInterface
    {
        $channel = $event->getSubject();

        if (!$channel instanceof ChannelInterface) {
            throw new UnexpectedTypeException(
                $channel,
                ChannelInterface::class,
            );
        }

        return $channel;
    }
}
