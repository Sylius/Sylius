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

namespace Tests\Sylius\Component\Locale\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Context\ProviderBasedLocaleContext;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ProviderBasedLocaleContextTest extends TestCase
{
    /** @var LocaleProviderInterface&MockObject */
    private MockObject $localeProvider;

    private ProviderBasedLocaleContext $providerBasedLocaleContext;

    protected function setUp(): void
    {
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->providerBasedLocaleContext = new ProviderBasedLocaleContext($this->localeProvider);
    }

    public function testALocaleContext(): void
    {
        self::assertInstanceOf(LocaleContextInterface::class, $this->providerBasedLocaleContext);
    }

    public function testReturnsTheChannelsDefaultLocale(): void
    {
        $this->localeProvider->expects($this->once())->method('getAvailableLocalesCodes')->willReturn(['pl_PL', 'en_US']);
        $this->localeProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        self::assertSame('pl_PL', $this->providerBasedLocaleContext->getLocaleCode());
    }

    public function testThrowsALocaleNotFoundExceptionIfDefaultLocaleIsNotAvailable(): void
    {
        $this->localeProvider->expects($this->once())->method('getAvailableLocalesCodes')->willReturn(['es_ES', 'en_US']);
        $this->localeProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('pl_PL');
        self::expectException(LocaleNotFoundException::class);
        $this->providerBasedLocaleContext->getLocaleCode();
    }
}
