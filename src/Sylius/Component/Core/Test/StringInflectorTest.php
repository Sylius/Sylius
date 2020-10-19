<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Test;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Formatter\StringInflector;

final class StringInflectorTest extends TestCase
{
    /**
     * @test
     */
    public function codeToName(): void
    {
        self::assertEquals('Test value', StringInflector::codeToName('test_value'));
    }

    /**
     * @test
     */
    public function nameToCode(): void
    {
        self::assertEquals('Test_value', StringInflector::nameToCode('Test value'));
    }

    /**
     * @test
     */
    public function nameToSlug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test value'));
    }

    /**
     * @test
     */
    public function nameWithSpecialCharactersToSlug(): void
    {
        self::assertEquals('test-value', StringInflector::nameToSlug('Test!%-value!'));
    }

    /**
     * @test
     */
    public function nameToLowercaseCode(): void
    {
        self::assertEquals('test_value', StringInflector::nameToLowercaseCode('Test value'));
    }

    /**
     * @test
     */
    public function nameToUppercaseCode(): void
    {
        self::assertEquals('TEST_VALUE', StringInflector::nameToUppercaseCode('Test value'));
    }

    /**
     * @test
     */
    public function nameToCamelCase(): void
    {
        self::assertEquals('testValue', StringInflector::nameToCamelCase('Test value'));
    }
}
