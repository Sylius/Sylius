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

namespace Tests\Sylius\Behat\Service\Setter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\Setter\ChannelContextSetter;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

final class ChannelContextSetterTest extends TestCase
{
    private CookieSetterInterface&MockObject $cookieSetter;

    private ChannelContextSetter $channelContextSetter;

    protected function setUp(): void
    {
        $this->cookieSetter = $this->createMock(CookieSetterInterface::class);

        $this->channelContextSetter = new ChannelContextSetter($this->cookieSetter);
    }

    public function testImplementsChannelContextSetterInterface(): void
    {
        $this->assertInstanceOf(ChannelContextSetterInterface::class, $this->channelContextSetter);
    }

    public function testSetsChannelAsCurrent(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $channel->expects($this->once())->method('getCode')->willReturn('CHANNEL_CODE');
        $this->cookieSetter->expects($this->once())->method('setCookie')->with('_channel_code', 'CHANNEL_CODE');

        $this->channelContextSetter->setChannel($channel);
    }
}
