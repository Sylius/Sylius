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

namespace Tests\Sylius\Component\Locale\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\CachedLocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class CachedLocaleCollectionProviderTest extends TestCase
{
    /** @var LocaleCollectionProviderInterface&MockObject */
    private MockObject $decorated;

    /** @var CacheInterface&MockObject */
    private MockObject $cache;

    private CachedLocaleCollectionProvider $cachedLocaleCollectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decorated = $this->createMock(LocaleCollectionProviderInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->cachedLocaleCollectionProvider = new CachedLocaleCollectionProvider($this->decorated, $this->cache);
    }

    public function testImplementsLocaleCollectionProviderInterface(): void
    {
        self::assertInstanceOf(LocaleCollectionProviderInterface::class, $this->cachedLocaleCollectionProvider);
    }

    public function testReturnsAllLocalesViaCache(): void
    {
        /** @var LocaleInterface&MockObject $someLocale */
        $someLocale = $this->createMock(LocaleInterface::class);
        /** @var LocaleInterface&MockObject $anotherLocale */
        $anotherLocale = $this->createMock(LocaleInterface::class);
        $someLocale->method('getCode')->willReturn('en_US');
        $anotherLocale->method('getCode')->willReturn('en_GB');
        $this->cache->expects($this->once())->method('get')
            ->with('sylius_locales', $this->isType('callable'))
            ->willReturnCallback(fn (string $key, callable $callback) => $callback());
        $this->decorated->expects($this->once())->method('getAll')
            ->willReturn(['en_US' => $someLocale, 'en_GB' => $anotherLocale]);
        self::assertSame(['en_US' => $someLocale, 'en_GB' => $anotherLocale], $this->cachedLocaleCollectionProvider->getAll());
    }
}
