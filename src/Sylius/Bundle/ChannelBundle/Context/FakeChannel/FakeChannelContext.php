<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ChannelBundle\Context\FakeChannel;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class FakeChannelContext implements ChannelContextInterface
{
    public function __construct(
        private FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        private ChannelRepositoryInterface $channelRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function getChannel(): ChannelInterface
    {
        $fakeChannelCode = $this->fakeChannelCodeProvider->getCode($this->getMainRequest());

        if (null === $fakeChannelCode) {
            throw new ChannelNotFoundException();
        }

        $channel = $this->channelRepository->findOneByCode($fakeChannelCode);

        if (null === $channel) {
            throw new ChannelNotFoundException();
        }

        return $channel;
    }

    /**
     * @throws ChannelNotFoundException
     */
    private function getMainRequest(): Request
    {
        if (\method_exists($this->requestStack, 'getMainRequest')) {
            $mainRequest = $this->requestStack->getMainRequest();
        } else {
            $mainRequest = $this->requestStack->getMasterRequest();
        }
        if (null === $mainRequest) {
            throw new ChannelNotFoundException();
        }

        return $mainRequest;
    }
}
