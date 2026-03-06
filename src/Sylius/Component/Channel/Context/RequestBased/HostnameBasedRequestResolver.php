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

namespace Sylius\Component\Channel\Context\RequestBased;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class HostnameBasedRequestResolver implements RequestResolverInterface
{
    private const LOCALHOST_EQUIVALENTS = ['localhost', '127.0.0.1', '::1'];

    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function findChannel(Request $request): ?ChannelInterface
    {
        $hostname = $request->getHost();

        $channel = $this->channelRepository->findOneEnabledByHostname($hostname);

        if (null !== $channel || !in_array($hostname, self::LOCALHOST_EQUIVALENTS, true)) {
            return $channel;
        }

        foreach (self::LOCALHOST_EQUIVALENTS as $localhostEquivalent) {
            if ($localhostEquivalent === $hostname) {
                continue;
            }

            $channel = $this->channelRepository->findOneEnabledByHostname($localhostEquivalent);
            if (null !== $channel) {
                return $channel;
            }
        }

        return null;
    }
}
