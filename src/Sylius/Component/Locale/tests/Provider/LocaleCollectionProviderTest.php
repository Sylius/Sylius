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
use Sylius\Component\Locale\Provider\LocaleCollectionProvider;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class LocaleCollectionProviderTest extends TestCase
{
    /** @var RepositoryInterface&MockObject */
    private MockObject $localeRepository;

    private LocaleCollectionProvider $localeCollectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeRepository = $this->createMock(RepositoryInterface::class);
        $this->localeCollectionProvider = new LocaleCollectionProvider($this->localeRepository);
    }

    public function testImplementsLocaleCollectionProviderInterface(): void
    {
        self::assertInstanceOf(LocaleCollectionProviderInterface::class, $this->localeCollectionProvider);
    }

    public function testReturnsAllLocales(): void
    {
        /** @var LocaleInterface&MockObject $someLocale */
        $someLocale = $this->createMock(LocaleInterface::class);
        /** @var LocaleInterface&MockObject $anotherLocale */
        $anotherLocale = $this->createMock(LocaleInterface::class);
        $someLocale->expects($this->once())->method('getCode')->willReturn('en_US');
        $anotherLocale->expects($this->once())->method('getCode')->willReturn('en_GB');
        $this->localeRepository->expects($this->once())->method('findAll')->willReturn([$someLocale, $anotherLocale]);
        self::assertSame(['en_US' => $someLocale, 'en_GB' => $anotherLocale], $this->localeCollectionProvider->getAll());
    }
}
