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

namespace Tests\Sylius\Component\Addressing\Provider;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProvider;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ProvinceNamingProviderTest extends TestCase
{
    /** @var RepositoryInterface<ProvinceInterface>&MockObject */
    private MockObject $provinceRepositoryMock;

    private ProvinceNamingProvider $provinceNamingProvider;

    protected function setUp(): void
    {
        $this->provinceRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->provinceNamingProvider = new ProvinceNamingProvider($this->provinceRepositoryMock);
    }

    public function testThrowsInvalidArgumentExceptionGettingNameWhenProvinceWithGivenCodeIsNotFound(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('ZZ-TOP');
        $this->provinceRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'ZZ-TOP'])->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->provinceNamingProvider->getName($addressMock);
    }

    public function testThrowsInvalidArgumentExceptionGettingAbbreviationWhenProvinceWithGivenCodeIsNotFound(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('ZZ-TOP');
        $this->provinceRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'ZZ-TOP'])->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->provinceNamingProvider->getAbbreviation($addressMock);
    }

    public function testGetsProvinceNameIfProvinceWithGivenCodeExistsInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('IE-UL');
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        $this->provinceRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects(self::once())->method('getName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getName($addressMock));
    }

    public function testGetsProvinceNameFromAddress(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getName($addressMock));
    }

    public function testReturnsNothingIfProvinceNameAndCodeAreNotGivenInAnAddress(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        self::assertSame('', $this->provinceNamingProvider->getName($addressMock));
        self::assertSame('', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceAbbreviationByItsCodeIfProvinceExistsInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('IE-UL');
        $this->provinceRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects(self::once())->method('getAbbreviation')->willReturn('ULS');
        self::assertSame('ULS', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceNameIfItsAbbreviationIsNotSetButProvinceExistsInDatabase(): void
    {
        /** @var ProvinceInterface&MockObject $provinceMock */
        $provinceMock = $this->createMock(ProvinceInterface::class);
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn(null);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceCode')->willReturn('IE-UL');
        $this->provinceRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'IE-UL'])->willReturn($provinceMock);
        $provinceMock->expects(self::once())->method('getAbbreviation')->willReturn(null);
        $provinceMock->expects(self::once())->method('getName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }

    public function testGetsProvinceNameFromAddressIfItsAbbreviationIsNotSet(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $addressMock->expects(self::atLeastOnce())->method('getProvinceName')->willReturn('Ulster');
        self::assertSame('Ulster', $this->provinceNamingProvider->getAbbreviation($addressMock));
    }
}
