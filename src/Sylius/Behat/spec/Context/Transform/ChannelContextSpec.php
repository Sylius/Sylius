<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ChannelContextSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->beConstructedWith(
            $channelRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\ChannelContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_transforms_channel_name_to_channel_object(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ) {
        $channelRepository->findOneBy(['name' => 'Store'])->willReturn($channel);

        $this->getChannelByName('Store')->shouldReturn($channel);
    }

    function it_throws_an_exception_if_channel_is_not_found(
        ChannelRepositoryInterface $channelRepository
    ) {
        $channelRepository->findOneBy(['name' => 'Store'])->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Channel with name "Store" does not exist'))->during('getChannelByName', ['Store']);
    }
}
