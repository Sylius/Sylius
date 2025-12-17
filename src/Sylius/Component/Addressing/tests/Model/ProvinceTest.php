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

namespace Tests\Sylius\Component\Addressing\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\Province;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Resource\Model\CodeAwareInterface;

final class ProvinceTest extends TestCase
{
    private Province $province;

    protected function setUp(): void
    {
        $this->province = new Province();
    }

    public function testImplementsSyliusCountryProvinceInterface(): void
    {
        self::assertInstanceOf(ProvinceInterface::class, $this->province);
    }

    public function testImplementsCodeAwareInterface(): void
    {
        self::assertInstanceOf(CodeAwareInterface::class, $this->province);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->province->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->province->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->province->setCode('US-TX');
        self::assertSame('US-TX', $this->province->getCode());
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->province->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->province->setName('Texas');
        self::assertSame('Texas', $this->province->getName());
    }

    public function testHasNoAbbreviationByDefault(): void
    {
        self::assertNull($this->province->getAbbreviation());
    }

    public function testItsAbbreviationIsMutable(): void
    {
        $this->province->setAbbreviation('TEX');
        self::assertSame('TEX', $this->province->getAbbreviation());
    }

    public function testDoesNotBelongToCountryByDefault(): void
    {
        self::assertNull($this->province->getCountry());
    }

    public function testAllowsToAttachItselfToACountry(): void
    {
        /** @var CountryInterface&MockObject $countryMock */
        $countryMock = $this->createMock(CountryInterface::class);
        $this->province->setCountry($countryMock);
        self::assertSame($countryMock, $this->province->getCountry());
    }
}
