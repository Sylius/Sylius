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

namespace Tests\Sylius\Component\Locale\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Model\TimestampableInterface;

final class LocaleTest extends TestCase
{
    private Locale $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locale = new Locale();
    }

    public function testImplementsALocaleInterface(): void
    {
        self::assertInstanceOf(LocaleInterface::class, $this->locale);
    }

    public function testTimestampable(): void
    {
        self::assertInstanceOf(TimestampableInterface::class, $this->locale);
    }

    public function testDoesNotHaveIdByDefault(): void
    {
        self::assertNull($this->locale->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->locale->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->locale->setCode('de_DE');
        self::assertSame('de_DE', $this->locale->getCode());
    }

    public function testHasAName(): void
    {
        $this->locale->setCode('pl_PL');
        self::assertSame('Polish (Poland)', $this->locale->getName());
        self::assertSame('polaco (Polonia)', $this->locale->getName('es'));

        $this->locale->setCode('pl');
        self::assertSame('Polish', $this->locale->getName());
        self::assertSame('polaco', $this->locale->getName('es'));
    }

    public function testReturnsNameWhenConvertedToString(): void
    {
        $this->locale->setCode('pl_PL');
        self::assertSame('Polish (Poland)', $this->locale->__toString());

        $this->locale->setCode('pl');
        self::assertSame('Polish', $this->locale->__toString());
    }

    public function testDoesNotHaveLastUpdateDateByDefault(): void
    {
        self::assertNull($this->locale->getUpdatedAt());
    }
}
