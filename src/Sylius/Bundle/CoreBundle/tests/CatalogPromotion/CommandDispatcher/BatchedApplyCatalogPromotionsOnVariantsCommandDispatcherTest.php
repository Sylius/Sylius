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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyCatalogPromotionsOnVariantsCommandDispatcherTest extends TestCase
{
    private MessageBusInterface&MockObject $messageBus;

    private BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->dispatcher = new BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher($this->messageBus, 2);
    }

    public function testImplementsApplyCatalogPromotionsOnVariantsCommandDispatcherInterface(): void
    {
        $this->assertInstanceOf(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class, $this->dispatcher);
    }

    public function testDispatchesInTwoBatches(): void
    {
        $expectedFirst = new ApplyCatalogPromotionsOnVariants(['example_variant1', 'example_variant2']);
        $expectedSecond = new ApplyCatalogPromotionsOnVariants(['example_variant3']);

        $calls = [];

        $this->messageBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function ($command) use (&$calls) {
                $calls[] = $command;

                return new Envelope($command);
            })
        ;

        $this->dispatcher->updateVariants(['example_variant1', 'example_variant2', 'example_variant3']);

        $this->assertCount(2, $calls);

        $this->assertEquals($expectedFirst, $calls[0]);
        $this->assertEquals($expectedSecond, $calls[1]);
    }

    public function testDispatchesInOneBatch(): void
    {
        $expected = new ApplyCatalogPromotionsOnVariants(['example_variant1', 'example_variant2']);

        $calls = [];

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($command) use (&$calls) {
                $calls[] = $command;

                return new Envelope($command);
            })
        ;

        $this->dispatcher->updateVariants(['example_variant1', 'example_variant2']);

        $this->assertCount(1, $calls);
        $this->assertEquals($expected, $calls[0]);
    }
}
