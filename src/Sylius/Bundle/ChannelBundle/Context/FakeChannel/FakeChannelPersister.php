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

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class FakeChannelPersister
{
    public function __construct(private FakeChannelCodeProviderInterface $fakeChannelCodeProvider)
    {
    }

    public function onKernelResponse(ResponseEvent $filterResponseEvent): void
    {
        if (HttpKernelInterface::SUB_REQUEST === $filterResponseEvent->getRequestType()) {
            return;
        }

        $fakeChannelCode = $this->fakeChannelCodeProvider->getCode($filterResponseEvent->getRequest());

        if (null === $fakeChannelCode) {
            return;
        }

        $response = $filterResponseEvent->getResponse();
        $response->headers->setCookie(new Cookie('_channel_code', $fakeChannelCode));
    }
}
