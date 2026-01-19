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

namespace Tests\Sylius\Component\Channel\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Factory\ChannelFactory;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;

class ChannelFactoryTest extends TestCase
{
    public function testShouldImplementChannelFactoryInterface(): void
    {
        $defaultFactory = $this->createMock(FactoryInterface::class);
        $factory = new ChannelFactory($defaultFactory);

        self::assertInstanceOf(ChannelFactoryInterface::class, $factory);
    }

    public function testShouldCreateChannelWithName(): void
    {
        $defaultFactory = $this->createMock(FactoryInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $defaultFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($channel);

        $channel->expects(self::once())
            ->method('setName')
            ->with('United States Webstore');

        $factory = new ChannelFactory($defaultFactory);

        self::assertSame($channel, $factory->createNamed('United States Webstore'));
    }

    public function testShouldCreateEmptyChannel(): void
    {
        $defaultFactory = $this->createMock(FactoryInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $defaultFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($channel);

        $factory = new ChannelFactory($defaultFactory);

        self::assertSame($channel, $factory->createNew());
    }
}
