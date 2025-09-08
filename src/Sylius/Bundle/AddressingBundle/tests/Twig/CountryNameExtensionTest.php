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

namespace Tests\Sylius\Bundle\AddressingBundle\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AddressingBundle\Twig\CountryNameExtension;
use Sylius\Component\Addressing\Model\CountryInterface;
use Twig\Extension\ExtensionInterface;

final class CountryNameExtensionTest extends TestCase
{
    private CountryNameExtension $countryNameExtension;

    protected function setUp(): void
    {
        $this->countryNameExtension = new CountryNameExtension();
    }

    public function testImplementsATwigExtension(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, $this->countryNameExtension);
    }

    public function testTranslatesCountryIsoCodeIntoName(): void
    {
        $this->assertSame('Ireland', $this->countryNameExtension->translateCountryIsoCode('IE'));
    }

    public function testTranslatesCountryIntoName(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        $country->expects($this->once())->method('getCode')->willReturn('IE');
        $this->assertSame('Ireland', $this->countryNameExtension->translateCountryIsoCode($country));
    }

    public function testTranslatesCountryCodeToNameAccordingToLocale(): void
    {
        $this->assertSame('Irlanda', $this->countryNameExtension->translateCountryIsoCode('IE', 'es'));
    }

    public function testFallbacksToCountryCodeWhenThereIsNoTranslation(): void
    {
        $this->assertSame(
            'country_code_without_translation',
            $this->countryNameExtension->translateCountryIsoCode('country_code_without_translation'),
        );
    }

    public function testFallbacksToAnEmptyStringWhenThereIsNoCode(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $country->expects($this->once())->method('getCode')->willReturn(null);
        $this->assertSame('', $this->countryNameExtension->translateCountryIsoCode($country));
        $this->assertSame('', $this->countryNameExtension->translateCountryIsoCode(null));
    }
}
