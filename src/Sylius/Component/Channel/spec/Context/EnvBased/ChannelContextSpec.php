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

namespace spec\Sylius\Component\Channel\Context\EnvBased;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class ChannelContextSpec extends ObjectBehavior
{
    private const SYLIUS_CHANNEL = 'FASHION_WEB';

    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository, self::SYLIUS_CHANNEL);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChannelContextInterface::class);
    }

    function it_returns_channel(ChannelRepositoryInterface $channelRepository, ChannelInterface $channel): void
    {
        $channelRepository->findOneByCode(self::SYLIUS_CHANNEL)->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_exception_when_channel_not_found(ChannelRepositoryInterface $channelRepository): void
    {
        $channelRepository->findOneByCode(self::SYLIUS_CHANNEL)->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
