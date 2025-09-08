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
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class CountryTest extends TestCase
{
    private Country $country;

    protected function setUp(): void
    {
        $this->country = new Country();
    }

    public function testImplementsSyliusCountryInterface(): void
    {
        self::assertInstanceOf(CountryInterface::class, $this->country);
    }

    public function testToggleable(): void
    {
        self::assertInstanceOf(ToggleableInterface::class, $this->country);
    }

    public function testImplementsCodeAwareInterface(): void
    {
        self::assertInstanceOf(CodeAwareInterface::class, $this->country);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->country->getId());
    }

    public function testReturnsNameWhenConvertedToString(): void
    {
        $this->country->setCode('VE');
        self::assertSame('Venezuela', $this->country->__toString());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->country->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->country->setCode('MX');
        self::assertSame('MX', $this->country->getCode());
    }

    public function testHasNoProvincesByDefault(): void
    {
        self::assertFalse($this->country->hasProvinces());
    }

    public function testAddsProvince(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        $this->country->addProvince($provinceMock);
        self::assertTrue($this->country->hasProvince($provinceMock));
    }

    public function testRemovesProvince(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        $this->country->addProvince($provinceMock);
        self::assertTrue($this->country->hasProvince($provinceMock));
        $this->country->removeProvince($provinceMock);
        self::assertFalse($this->country->hasProvince($provinceMock));
    }

    public function testSetsCountryOnAddedProvince(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        $provinceMock->expects(self::once())->method('setCountry')->with($this->country);
        $this->country->addProvince($provinceMock);
    }

    public function testUnsetsCountryOnRemovedProvince(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        $this->country->addProvince($provinceMock);
        self::assertTrue($this->country->hasProvince($provinceMock));
        $provinceMock->expects(self::once())->method('setCountry')->with(null);
        $this->country->removeProvince($provinceMock);
    }

    public function testEnabledByDefault(): void
    {
        self::assertTrue($this->country->isEnabled());
    }

    public function testCanBeDisabled(): void
    {
        $this->country->disable();
        self::assertFalse($this->country->isEnabled());
    }

    public function testCanBeEnabled(): void
    {
        $this->country->disable();
        self::assertFalse($this->country->isEnabled());

        $this->country->enable();
        self::assertTrue($this->country->isEnabled());
    }

    public function testCanSetEnabledValue(): void
    {
        $this->country->setEnabled(false);
        self::assertFalse($this->country->isEnabled());

        $this->country->setEnabled(true);
        self::assertTrue($this->country->isEnabled());

        $this->country->setEnabled(false);
        self::assertFalse($this->country->isEnabled());
    }
}
