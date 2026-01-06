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

namespace Tests\Sylius\Component\Locale\Converter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Converter\LocaleConverter;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

final class LocaleConverterTest extends TestCase
{
    private LocaleConverter $localeConverter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeConverter = new LocaleConverter();
    }

    public function testALocaleConverter(): void
    {
        self::assertInstanceOf(LocaleConverterInterface::class, $this->localeConverter);
    }

    public function testConvertsLocaleNameToLocaleCode(): void
    {
        self::assertSame('de', $this->localeConverter->convertNameToCode('German'));
        self::assertSame('no', $this->localeConverter->convertNameToCode('Norwegian'));
        self::assertSame('pl', $this->localeConverter->convertNameToCode('Polish'));
    }

    public function testConvertsLocaleCodeToLocaleName(): void
    {
        self::assertSame('German', $this->localeConverter->convertCodeToName('de'));
        self::assertSame('Norwegian', $this->localeConverter->convertCodeToName('no'));
        self::assertSame('Polish', $this->localeConverter->convertCodeToName('pl'));
    }

    public function testThrowsInvalidArgumentExceptionIfCannotConvertNameToCode(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->localeConverter->convertNameToCode('xyz');
    }

    public function testThrowsInvalidArgumentExceptionIfCannotConvertCodeToName(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->localeConverter->convertCodeToName('xyz');
    }
}
