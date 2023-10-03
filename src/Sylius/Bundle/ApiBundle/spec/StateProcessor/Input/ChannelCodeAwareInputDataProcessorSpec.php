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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

final class ChannelCodeAwareInputDataProcessorSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($channelContext);
    }

    function it_adds_channel_code_to_object(
        ChannelCodeAwareInterface $command,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        Operation $operation,
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('CODE');

        $command->setChannelCode('CODE')->shouldBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_can_process_data_that_implements_channel_code_aware_interface(
        ChannelCodeAwareInterface $command,
        LocaleCodeAwareInterface $wrongCommand,
        Operation $operation,
    ): void {
        $this->supports($command, $operation)->shouldReturn(true);
        $this->supports($wrongCommand, $operation)->shouldReturn(false);
    }
}
