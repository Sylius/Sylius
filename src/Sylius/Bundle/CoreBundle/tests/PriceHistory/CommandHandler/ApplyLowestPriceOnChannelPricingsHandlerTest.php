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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler\ApplyLowestPriceOnChannelPricingsHandler;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ApplyLowestPriceOnChannelPricingsHandlerTest extends TestCase
{
    private MockObject&ProductLowestPriceBeforeDiscountProcessorInterface $processor;

    private MockObject&RepositoryInterface $channelPricingRepository;

    private ApplyLowestPriceOnChannelPricingsHandler $handler;

    protected function setUp(): void
    {
        $this->processor = $this->createMock(ProductLowestPriceBeforeDiscountProcessorInterface::class);
        $this->channelPricingRepository = $this->createMock(RepositoryInterface::class);

        $this->handler = new ApplyLowestPriceOnChannelPricingsHandler(
            $this->processor,
            $this->channelPricingRepository,
        );
    }

    public function testIsInitializable(): void
    {
        $this->assertInstanceOf(ApplyLowestPriceOnChannelPricingsHandler::class, $this->handler);
    }

    public function testAppliesLowestPriceBeforeDiscountOnAllGivenChannelPricings(): void
    {
        $first = $this->createMock(ChannelPricingInterface::class);
        $second = $this->createMock(ChannelPricingInterface::class);
        $third = $this->createMock(ChannelPricingInterface::class);

        $this->channelPricingRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['id' => [1, 3, 4]])
            ->willReturn([$first, $second, $third])
        ;

        $calls = [];
        $this->processor
            ->expects($this->exactly(3))
            ->method('process')
            ->willReturnCallback(function ($channelPricing) use (&$calls) {
                $calls[] = $channelPricing;
            })
        ;

        ($this->handler)(new ApplyLowestPriceOnChannelPricings([1, 3, 4]));

        $this->assertContains($first, $calls);
        $this->assertContains($second, $calls);
        $this->assertContains($third, $calls);
    }

    public function testDoesNotApplyAnythingWhenNoChannelPricingsProvided(): void
    {
        $this->channelPricingRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['id' => []])
            ->willReturn([])
        ;

        $this->processor
            ->expects($this->never())
            ->method('process')
        ;

        ($this->handler)(new ApplyLowestPriceOnChannelPricings([]));
    }
}
