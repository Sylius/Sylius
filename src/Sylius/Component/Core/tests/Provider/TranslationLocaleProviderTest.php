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

namespace Tests\Sylius\Component\Core\Provider;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Provider\TranslationLocaleProvider;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;

#[AllowMockObjectsWithoutExpectations]
final class TranslationLocaleProviderTest extends TestCase
{
    private LocaleCollectionProviderInterface&MockObject $localeCollectionProvider;

    private LocaleInterface&MockObject $localePl;

    private LocaleInterface&MockObject $localeEn;

    private TranslationLocaleProvider $provider;

    protected function setUp(): void
    {
        $this->localeCollectionProvider = $this->createMock(LocaleCollectionProviderInterface::class);
        $this->localePl = $this->createMock(LocaleInterface::class);
        $this->localeEn = $this->createMock(LocaleInterface::class);

        $this->provider = new TranslationLocaleProvider($this->localeCollectionProvider, 'pl_PL');
    }

    public function testShouldImplementTranslationLocaleProviderInterface(): void
    {
        $this->assertInstanceOf(TranslationLocaleProviderInterface::class, $this->provider);
    }

    public function testShouldReturnDefinedLocalesCodesWithDefaultLocaleFirst(): void
    {
        $this->localePl->expects($this->once())->method('getCode')->willReturn('pl_PL');
        $this->localeEn->expects($this->once())->method('getCode')->willReturn('en_US');

        $this->localeCollectionProvider
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$this->localeEn, $this->localePl]);

        $codes = $this->provider->getDefinedLocalesCodes();

        $this->assertSame(['pl_PL', 'en_US'], $codes);
    }

    public function testShouldReturnDefinedLocalesCodesWithoutDefaultLocaleIfNotPresent(): void
    {
        $this->localeEn->expects($this->once())->method('getCode')->willReturn('en_US');

        $this->localeCollectionProvider
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$this->localeEn]);

        $codes = $this->provider->getDefinedLocalesCodes();

        $this->assertSame(['en_US'], $codes);
    }

    public function testShouldReturnDefaultLocaleCode(): void
    {
        $this->assertSame('pl_PL', $this->provider->getDefaultLocaleCode());
    }
}
