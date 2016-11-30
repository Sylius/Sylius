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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FakeChannelContext implements ChannelContextInterface
{
    /**
     * @var FakeChannelCodeProviderInterface
     */
    private $fakeChannelCodeProvider;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FakeChannelCodeProviderInterface $fakeChannelCodeProvider
     * @param ChannelRepositoryInterface $channelRepository
     * @param RequestStack $requestStack
     */
    public function __construct(
        FakeChannelCodeProviderInterface $fakeChannelCodeProvider,
        ChannelRepositoryInterface $channelRepository,
        RequestStack $requestStack
    ) {
        $this->fakeChannelCodeProvider = $fakeChannelCodeProvider;
        $this->channelRepository = $channelRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        $fakeChannelCode = $this->fakeChannelCodeProvider->getCode($this->getMasterRequest());

        $channel = $this->channelRepository->findOneByCode($fakeChannelCode);

        $this->assertChannelWasFound($channel);

        return $channel;
    }

    /**
     * @return Request
     *
     * @throws ChannelNotFoundException
     */
    private function getMasterRequest()
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new ChannelNotFoundException();
        }

        return $masterRequest;
    }

    /**
     * @param ChannelInterface|null $channel
     *
     * @throws ChannelNotFoundException
     */
    private function assertChannelWasFound(ChannelInterface $channel = null)
    {
        if (null === $channel) {
            throw new ChannelNotFoundException();
        }
    }
}
