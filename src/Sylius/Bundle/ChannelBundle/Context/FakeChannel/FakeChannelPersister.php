<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FakeChannelPersister
{
    /**
     * @var FakeChannelCodeProviderInterface
     */
    private $fakeChannelCodeProvider;

    /**
     * @param FakeChannelCodeProviderInterface $fakeChannelCodeProvider
     */
    public function __construct(FakeChannelCodeProviderInterface $fakeChannelCodeProvider)
    {
        $this->fakeChannelCodeProvider = $fakeChannelCodeProvider;
    }

    /**
     * @param FilterResponseEvent $filterResponseEvent
     */
    public function onKernelResponse(FilterResponseEvent $filterResponseEvent)
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
