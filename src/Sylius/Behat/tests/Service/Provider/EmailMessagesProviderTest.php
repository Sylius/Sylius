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

namespace Tests\Sylius\Behat\Service\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Behat\Service\MessageSendCacher;
use Sylius\Behat\Service\Provider\EmailMessagesProvider;
use Sylius\Behat\Service\Provider\EmailMessagesProviderInterface;
use Symfony\Component\Mime\Email;

final class EmailMessagesProviderTest extends TestCase
{
    private CacheItemPoolInterface&MockObject $cacheItemPool;

    private EmailMessagesProvider $emailMessagesProvider;

    protected function setUp(): void
    {
        $this->cacheItemPool = $this->createMock(CacheItemPoolInterface::class);

        $this->emailMessagesProvider = new EmailMessagesProvider($this->cacheItemPool);
    }

    public function testImplementsEmailMessagesProviderInterface(): void
    {
        $this->assertInstanceOf(EmailMessagesProviderInterface::class, $this->emailMessagesProvider);
    }

    public function testProvidesEmailMessages(): void
    {
        /** @var CacheItemInterface&MockObject $cacheItem */
        $cacheItem = $this->createMock(CacheItemInterface::class);

        $emailMessages = [new Email(), new Email(), new Email()];
        $cacheItem->expects($this->once())->method('get')->willReturn($emailMessages);
        $this->cacheItemPool->expects($this->once())->method('hasItem')->with(MessageSendCacher::CACHE_KEY)->willReturn(true);
        $this->cacheItemPool->expects($this->once())->method('getItem')->with(MessageSendCacher::CACHE_KEY)->willReturn($cacheItem);

        $this->assertSame($emailMessages, $this->emailMessagesProvider->provide());
    }

    public function testReturnsAnEmptyArrayIfCacheKeyDoesNotExist(): void
    {
        $this->cacheItemPool->expects($this->once())->method('hasItem')->with(MessageSendCacher::CACHE_KEY)->willReturn(false);

        $this->assertSame([], $this->emailMessagesProvider->provide());
    }
}
