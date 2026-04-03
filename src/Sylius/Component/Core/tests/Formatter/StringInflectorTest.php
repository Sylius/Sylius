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

namespace Tests\Sylius\Component\Core\Formatter;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Formatter\StringInflector;

final class StringInflectorTest extends TestCase
{
    public function testShouldConvertNameToCode(): void
    {
        self::assertEquals('Test_value', StringInflector::nameToCode('Test value'));
    }

    public function testShouldConvertNameWithSpecialCharactersToCode(): void
    {
        self::assertEquals('Test?_value!', StringInflector::nameToCode('Test? value!'));
    }

    public function testShouldConvertNameToSlug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test value'));
    }

    public function testShouldConvertNameWithSpecialCharactersToSlug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test!%-value!'));
    }

    public function testShouldConvertNameToLowercaseCode(): void
    {
        self::assertEquals('test_value', StringInflector::nameToLowercaseCode('Test value'));
    }

    public function testShouldConvertNameWithSpecialCharactersToLowercaseCode(): void
    {
        self::assertEquals('test?_value!', StringInflector::nameToLowercaseCode('Test? value!'));
    }

    public function testShouldConvertNameToUppercaseCode(): void
    {
        self::assertEquals('TEST_VALUE', StringInflector::nameToUppercaseCode('Test value'));
    }

    public function testShouldConvertNameWithSpecialCharactersToUpperCaseCode(): void
    {
        self::assertEquals('TEST?_VALUE!', StringInflector::nameToUppercaseCode('Test? value!'));
    }

    public function testShouldConvertNameToCamelCase(): void
    {
        self::assertEquals('testValue', StringInflector::nameToCamelCase('Test value'));
    }

    public function testShouldConvertNameWithSpecialCharactersToCamelCase(): void
    {
        self::assertEquals('testValue', StringInflector::nameToCamelCase('Test? value!'));
    }
}
