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

namespace Tests\Sylius\Component\Currency\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;

final class CurrencyTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currency = new Currency();
    }

    public function testShouldImplementCurrencyInterface(): void
    {
        self::assertInstanceOf(CurrencyInterface::class, $this->currency);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->currency->getId());
    }

    public function testShouldHaveNoCodeByDefault(): void
    {
        self::assertNull($this->currency->getCode());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->currency->setCode('RSD');
        self::assertSame('RSD', $this->currency->getCode());
    }

    public function testShouldNotReturnNameWhenItHasNoCode(): void
    {
        $this->currency->setCode(null);
        self::assertNull($this->currency->getName());
    }

    public function testShouldReturnNameOfCurrencyCode(): void
    {
        $this->currency->setCode('EUR');
        self::assertSame('Euro', $this->currency->getName());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->currency->getCreatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->currency->setCreatedAt($date);
        self::assertSame($date, $this->currency->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->currency->getUpdatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->currency->setUpdatedAt($date);
        self::assertSame($date, $this->currency->getUpdatedAt());
    }
}
