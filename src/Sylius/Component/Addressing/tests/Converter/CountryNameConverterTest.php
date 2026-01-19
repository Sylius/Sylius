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

namespace Tests\Sylius\Component\Addressing\Converter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Converter\CountryNameConverter;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;

final class CountryNameConverterTest extends TestCase
{
    private CountryNameConverterInterface $countryNameConverter;

    protected function setUp(): void
    {
        $this->countryNameConverter = new CountryNameConverter();
    }

    public function testImplementsCountryNameToCodeConverterInterface(): void
    {
        self::assertInstanceOf(CountryNameConverterInterface::class, $this->countryNameConverter);
    }

    public function testConvertsEnglishCountryNameToCodesByDefault(): void
    {
        self::assertSame('AU', $this->countryNameConverter->convertToCode('Australia'));
        self::assertSame('CN', $this->countryNameConverter->convertToCode('China'));
        self::assertSame('FR', $this->countryNameConverter->convertToCode('France'));
    }

    public function testConvertsCountryNameToCodesForGivenLocale(): void
    {
        self::assertSame('DE', $this->countryNameConverter->convertToCode('Niemcy', 'pl'));
        self::assertSame('CN', $this->countryNameConverter->convertToCode('Chine', 'fr'));
        self::assertSame('FR', $this->countryNameConverter->convertToCode('Francia', 'es'));
    }

    public function testThrowsAnExceptionIfCountryNameCannotBeConvertedToCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->countryNameConverter->convertToCode('Atlantis');
    }
}
