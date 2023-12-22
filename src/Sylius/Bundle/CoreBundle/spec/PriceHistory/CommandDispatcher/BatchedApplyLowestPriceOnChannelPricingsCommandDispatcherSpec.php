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

namespace spec\Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyLowestPriceOnChannelPricingsCommandDispatcherSpec extends ObjectBehavior
{
    function let(RepositoryInterface $channelPricingRepository, MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($channelPricingRepository, $commandBus, 2);
    }

    function it_implements_apply_lowest_price_on_channel_pricings_command_dispatcher_interface(): void
    {
        $this->shouldImplement(ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface::class);
    }

    function it_dispatches_applications_of_lowest_price_on_channel_pricing_within_channel_in_batches(
        RepositoryInterface $channelPricingRepository,
        MessageBusInterface $commandBus,
        ChannelInterface $channel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ChannelPricingInterface $thirdChannelPricing,
        ChannelPricingInterface $fourthChannelPricing,
        ChannelPricingInterface $fifthChannelPricing,
    ): void {
        $channel->getCode()->willReturn('WEB');

        $firstChannelPricing->getId()->willReturn(1);
        $secondChannelPricing->getId()->willReturn(2);
        $thirdChannelPricing->getId()->willReturn(6);
        $fourthChannelPricing->getId()->willReturn(7);
        $fifthChannelPricing->getId()->willReturn(9);

        $batches = [
            [
                $firstChannelPricing->getWrappedObject(),
                $secondChannelPricing->getWrappedObject(),
            ],
            [
                $thirdChannelPricing->getWrappedObject(),
                $fourthChannelPricing->getWrappedObject(),
            ],
            [
                $fifthChannelPricing->getWrappedObject(),
            ],
            [],
        ];

        $batchSize = 2;

        foreach ($batches as $key => $batch) {
            $channelPricingRepository
                ->findBy(['channelCode' => 'WEB'], ['id' => 'ASC'], 2, $key * $batchSize)
                ->willReturn($batch)
                ->shouldBeCalled()
            ;
        }

        foreach ([[1, 2], [6, 7], [9]] as $ids) {
            $commandBus
                ->dispatch($command = new ApplyLowestPriceOnChannelPricings($ids))
                ->willReturn(new Envelope($command))
                ->shouldBeCalled()
            ;
        }

        $this->applyWithinChannel($channel);
    }
}
