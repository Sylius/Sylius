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

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ResetInterface;

final class CachedPerRequestChannelContext implements ChannelContextInterface, ResetInterface
{
    private \SplObjectStorage $requestToChannelMap;

    private \SplObjectStorage $requestToExceptionMap;

    public function __construct(private ChannelContextInterface $decoratedChannelContext, private RequestStack $requestStack)
    {
        $this->requestToChannelMap = new \SplObjectStorage();
        $this->requestToExceptionMap = new \SplObjectStorage();
    }

    public function getChannel(): ChannelInterface
    {
        $objectIdentifier = $this->requestStack->getMainRequest();
        if (null === $objectIdentifier) {
            return $this->decoratedChannelContext->getChannel();
        }

        if (isset($this->requestToExceptionMap[$objectIdentifier])) {
            throw $this->requestToExceptionMap[$objectIdentifier];
        }

        try {
            if (!isset($this->requestToChannelMap[$objectIdentifier])) {
                $this->requestToChannelMap[$objectIdentifier] = $this->decoratedChannelContext->getChannel();
            }

            return $this->requestToChannelMap[$objectIdentifier];
        } catch (ChannelNotFoundException $exception) {
            $this->requestToExceptionMap[$objectIdentifier] = $exception;

            throw $exception;
        }
    }

    /**
     * Both maps are keyed by the Request object and nothing is ever removed, so on a
     * long-running runtime every request ever served stays reachable. Worse, a cached
     * Channel entity outlives the EntityManager that loaded it, and the next request's
     * flush fails with ORMInvalidArgumentException. New instances drop both.
     */
    public function reset(): void
    {
        $this->requestToChannelMap = new \SplObjectStorage();
        $this->requestToExceptionMap = new \SplObjectStorage();
    }
}
