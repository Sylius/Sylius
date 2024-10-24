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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ChannelContext implements ChannelContextInterface
{
    public function __construct(private RequestResolverInterface $requestResolver, private RequestStack $requestStack)
    {
    }

    public function getChannel(): ChannelInterface
    {
        try {
            return $this->getChannelForRequest($this->getMainRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new ChannelNotFoundException(null, $exception);
        }
    }

    private function getChannelForRequest(Request $request): ChannelInterface
    {
        $channel = $this->requestResolver->findChannel($request);

        $this->assertChannelWasFound($channel);

        return $channel;
    }

    private function getMainRequest(): Request
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if (null === $mainRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $mainRequest;
    }

    private function assertChannelWasFound(?ChannelInterface $channel): void
    {
        if (null === $channel) {
            throw new \UnexpectedValueException('Channel was not found for given request');
        }
    }
}
