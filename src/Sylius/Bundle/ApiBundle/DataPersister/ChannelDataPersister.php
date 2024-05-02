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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private ChannelDeletionCheckerInterface $channelDeletionChecker,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof ChannelInterface;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function persist($data, array $context = [])
    {
        return $this->decoratedDataPersister->persist($data, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remove($data, array $context = []): void
    {
        if (!$this->channelDeletionChecker->isDeletable($data)) {
            throw new ChannelCannotBeRemoved('The channel cannot be deleted. At least one enabled channel is required.');
        }

        $this->decoratedDataPersister->remove($data, $context);
    }
}
