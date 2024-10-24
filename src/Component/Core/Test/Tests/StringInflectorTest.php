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

namespace Sylius\Component\Core\Test\Tests;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Formatter\StringInflector;

final class StringInflectorTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_name_to_code(): void
    {
        self::assertEquals('Test_value', StringInflector::nameToCode('Test value'));
    }

    /**
     * @test
     */
    public function it_converts_name_with_special_characters_to_code(): void
    {
        self::assertEquals('Test?_value!', StringInflector::nameToCode('Test? value!'));
    }

    /**
     * @test
     */
    public function it_converts_name_to_slug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test value'));
    }

    /**
     * @test
     */
    public function it_converts_name_with_special_characters_to_slug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test!%-value!'));
    }

    /**
     * @test
     */
    public function it_converts_name_to_lowercase_code(): void
    {
        self::assertEquals('test_value', StringInflector::nameToLowercaseCode('Test value'));
    }

    /**
     * @test
     */
    public function it_converts_name_with_special_characters_to_lowercase_code(): void
    {
        self::assertEquals('test?_value!', StringInflector::nameToLowercaseCode('Test? value!'));
    }

    /**
     * @test
     */
    public function it_converts_name_to_upper_case_code(): void
    {
        self::assertEquals('TEST_VALUE', StringInflector::nameToUppercaseCode('Test value'));
    }

    /**
     * @test
     */
    public function it_converts_name_with_special_characters_to_upper_case_code(): void
    {
        self::assertEquals('TEST?_VALUE!', StringInflector::nameToUppercaseCode('Test? value!'));
    }

    /**
     * @test
     */
    public function it_converts_name_to_camel_case(): void
    {
        self::assertEquals('testValue', StringInflector::nameToCamelCase('Test value'));
    }

    /**
     * @test
     */
    public function it_converts_name_with_special_characters_to_camel_case(): void
    {
        self::assertEquals('testValue', StringInflector::nameToCamelCase('Test? value!'));
    }
}
