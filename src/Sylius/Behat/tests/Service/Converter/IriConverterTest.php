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

namespace Tests\Sylius\Behat\Service\Converter;

use ApiPlatform\Metadata\IriConverterInterface as BaseIriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Service\Converter\IriConverter;
use Sylius\Behat\Service\Converter\IriConverterInterface;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixes;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;

final class IriConverterTest extends TestCase
{
    private BaseIriConverterInterface&MockObject $decoratedIriConverter;

    private MockObject&OperationResolverInterface $operationResolver;

    private IriConverter $iriConverter;

    protected function setUp(): void
    {
        $this->decoratedIriConverter = $this->createMock(BaseIriConverterInterface::class);
        $this->operationResolver = $this->createMock(OperationResolverInterface::class);

        $this->iriConverter = new IriConverter($this->decoratedIriConverter, $this->operationResolver);
    }

    public function testImplementsTheBehatIriConverterInterface(): void
    {
        $this->assertInstanceOf(IriConverterInterface::class, $this->iriConverter);
    }

    public function testUsesInnerIriConverterToGetResourceFromIri(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $this->decoratedIriConverter->expects($this->once())->method('getResourceFromIri')->with('api/v2/admin/countries/CODE', [], null)->willReturn($country);

        $this->assertSame($country, $this->iriConverter->getResourceFromIri('api/v2/admin/countries/CODE'));
    }

    public function testUsesInnerIriConverterToGetIriFromResource(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);

        $this->decoratedIriConverter
            ->expects($this->once())
            ->method('getIriFromResource')
            ->with($country, UrlGeneratorInterface::ABS_PATH, null, [])
            ->willReturn('api/v2/admin/countries/CODE')
        ;

        $this->assertSame('api/v2/admin/countries/CODE', $this->iriConverter->getIriFromResource($country));
    }

    public function testProvidesIriFromResourceInGivenSection(): void
    {
        /** @var CountryInterface&MockObject $country */
        $country = $this->createMock(CountryInterface::class);
        /** @var Operation&MockObject $operation */
        $operation = $this->createMock(Operation::class);

        $this->operationResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(Country::class, PathPrefixes::ADMIN_PREFIX, null)
            ->willReturn($operation)
        ;
        $this->decoratedIriConverter
            ->expects($this->once())
            ->method('getIriFromResource')
            ->with($country, UrlGeneratorInterface::ABS_PATH, $operation, ['force_resource_class' => Country::class])
            ->willReturn('api/v2/admin/countries/CODE')
        ;

        $this->assertSame(
            'api/v2/admin/countries/CODE',
            $this->iriConverter->getIriFromResourceInSection(
                $country,
                PathPrefixes::ADMIN_PREFIX,
                UrlGeneratorInterface::ABS_PATH,
                null,
                [
                    'force_resource_class' => Country::class,
                ],
            ),
        )
        ;
    }
}
