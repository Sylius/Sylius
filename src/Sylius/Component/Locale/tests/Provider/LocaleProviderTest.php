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
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Component\Locale\Provider\LocaleProvider;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class LocaleProviderTest extends TestCase
{
    /** @var LocaleCollectionProviderInterface&MockObject */
    private MockObject $localeCollectionProviderMock;

    private LocaleProvider $localeProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeCollectionProviderMock = $this->createMock(LocaleCollectionProviderInterface::class);
        $this->localeProvider = new LocaleProvider($this->localeCollectionProviderMock, 'pl_PL');
    }

    public function testALocaleProviderInterface(): void
    {
        self::assertInstanceOf(LocaleProviderInterface::class, $this->localeProvider);
    }

    public function testReturnsAllEnabledLocales(): void
    {
        /** @var LocaleInterface&MockObject $locale */
        $locale = $this->createMock(LocaleInterface::class);
        $this->localeCollectionProviderMock->expects($this->once())->method('getAll')->willReturn([$locale]);
        $locale->expects($this->once())->method('getCode')->willReturn('en_US');
        self::assertSame(['en_US'], $this->localeProvider->getAvailableLocalesCodes());
    }

    public function testReturnsTheDefaultLocale(): void
    {
        self::assertSame('pl_PL', $this->localeProvider->getDefaultLocaleCode());
    }
}
