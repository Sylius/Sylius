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

namespace Tests\Sylius\Component\Currency\Converter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Converter\CurrencyNameConverter;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;

final class CurrencyNameConverterTest extends TestCase
{
    private CurrencyNameConverter $currencyNameConverter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyNameConverter = new CurrencyNameConverter();
    }

    public function testShouldImplementACurrencyNameConverterInterface(): void
    {
        self::assertInstanceOf(CurrencyNameConverterInterface::class, $this->currencyNameConverter);
    }

    public function testShouldConvertAnEnglishCurrencyNameToCodeByDefault(): void
    {
        $result = $this->currencyNameConverter->convertToCode('Euro');
        self::assertSame('EUR', $result);
    }

    public function testShouldConvertANameToACodeForGivenLocale(): void
    {
        $result = $this->currencyNameConverter->convertToCode('rupia indyjska', 'pl');
        self::assertSame('INR', $result);
    }

    public function testShouldThrowAnInvalidArgumentExceptionWhenCurrencyDoesNotExist(): void
    {
        self::expectException(InvalidArgumentException::class);

        $this->currencyNameConverter->convertToCode('Meuro');
    }
}
