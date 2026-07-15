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

namespace Tests\Sylius\Bundle\ApiBundle\ApiPlatform\Routing;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Routing\IriConverter;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Routing\RouterInterface;

final class IriConverterTest extends TestCase
{
    private IriConverterInterface&MockObject $decoratedIriConverter;

    private MockObject&PathPrefixProviderInterface $pathPrefixProvider;

    private MockObject&OperationResolverInterface $operationResolver;

    private MockObject&RouterInterface $router;

    private MockObject&ResourceClassResolverInterface $resourceClassResolver;

    private IriConverter $iriConverter;

    private CountryInterface&MockObject $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedIriConverter = $this->createMock(IriConverterInterface::class);
        $this->pathPrefixProvider = $this->createMock(PathPrefixProviderInterface::class);
        $this->operationResolver = $this->createMock(OperationResolverInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->resourceClassResolver = $this->createMock(ResourceClassResolverInterface::class);
        $this->iriConverter = new IriConverter(
            $this->decoratedIriConverter,
            $this->pathPrefixProvider,
            $this->operationResolver,
            $this->router,
            $this->resourceClassResolver,
        );
        $this->country = $this->createMock(CountryInterface::class);
    }

    public function testImplementsTheIriConverterInterface(): void
    {
        self::assertInstanceOf(IriConverterInterface::class, $this->iriConverter);
    }

    public function testUsesInnerIriConverterToGetResourceFromIri(): void
    {
        $this->decoratedIriConverter->expects(self::once())
            ->method('getResourceFromIri')
            ->with('api/v2/admin/countries/CODE', [], null)
            ->willReturn($this->country);

        self::assertSame($this->country, $this->iriConverter->getResourceFromIri('api/v2/admin/countries/CODE'));
    }

    public function testUsesOperationResolverToGetProperIriFromResource(): void
    {
        /** @var Operation&MockObject $operation */
        $operation = $this->createMock(Operation::class);

        $this->pathPrefixProvider->expects(self::once())
            ->method('getPathPrefix')
            ->with('api/v2/admin/countries')
            ->willReturn('admin');

        $this->operationResolver->expects(self::once())
            ->method('resolve')
            ->with(Country::class, 'admin', null)
            ->willReturn($operation);

        $this->decoratedIriConverter->expects(self::once())
            ->method('getIriFromResource')
            ->with($this->country, UrlGeneratorInterface::ABS_PATH, $operation, [
                'request_uri' => 'api/v2/admin/countries',
                'force_resource_class' => Country::class,
            ])
            ->willReturn('api/v2/admin/countries/CODE');

        self::assertSame('api/v2/admin/countries/CODE', $this->iriConverter
            ->getIriFromResource(
                $this->country,
                UrlGeneratorInterface::ABS_PATH,
                null,
                [
                    'request_uri' => 'api/v2/admin/countries',
                    'force_resource_class' => Country::class,
                ],
            ));
    }

    public function testResolvesDiscriminatorSubclassToParentResourceClassBeforeResolvingOperation(): void
    {
        /** @var Operation&MockObject $shopOperation */
        $shopOperation = $this->createMock(Operation::class);

        $concreteSubclassInstance = new Country();

        $this->resourceClassResolver->expects(self::once())
            ->method('isResourceClass')
            ->with(Country::class)
            ->willReturn(true);

        $this->resourceClassResolver->expects(self::once())
            ->method('getResourceClass')
            ->with($concreteSubclassInstance)
            ->willReturn(CountryInterface::class);

        $this->pathPrefixProvider->expects(self::once())
            ->method('getPathPrefix')
            ->with('api/v2/shop/countries')
            ->willReturn('shop');

        $this->operationResolver->expects(self::once())
            ->method('resolve')
            ->with(CountryInterface::class, 'shop', null)
            ->willReturn($shopOperation);

        $this->decoratedIriConverter->expects(self::once())
            ->method('getIriFromResource')
            ->with(self::identicalTo($concreteSubclassInstance), UrlGeneratorInterface::ABS_PATH, $shopOperation, [
                'request_uri' => 'api/v2/shop/countries',
            ])
            ->willReturn('api/v2/shop/countries/CODE');

        self::assertSame(
            'api/v2/shop/countries/CODE',
            $this->iriConverter->getIriFromResource(
                $concreteSubclassInstance,
                UrlGeneratorInterface::ABS_PATH,
                null,
                ['request_uri' => 'api/v2/shop/countries'],
            ),
        );
    }

    public function testUsesConcreteClassDirectlyWhenItIsNotAKnownResourceClass(): void
    {
        /** @var Operation&MockObject $operation */
        $operation = $this->createMock(Operation::class);

        $concreteInstance = new Country();

        $this->resourceClassResolver->expects(self::once())
            ->method('isResourceClass')
            ->with(Country::class)
            ->willReturn(false);

        $this->resourceClassResolver->expects(self::never())
            ->method('getResourceClass');

        $this->pathPrefixProvider->expects(self::once())
            ->method('getPathPrefix')
            ->with('api/v2/shop/countries')
            ->willReturn('shop');

        $this->operationResolver->expects(self::once())
            ->method('resolve')
            ->with(Country::class, 'shop', null)
            ->willReturn($operation);

        $this->decoratedIriConverter->expects(self::once())
            ->method('getIriFromResource')
            ->with(self::identicalTo($concreteInstance), UrlGeneratorInterface::ABS_PATH, $operation, [
                'request_uri' => 'api/v2/shop/countries',
            ])
            ->willReturn('api/v2/shop/countries/CODE');

        self::assertSame(
            'api/v2/shop/countries/CODE',
            $this->iriConverter->getIriFromResource(
                $concreteInstance,
                UrlGeneratorInterface::ABS_PATH,
                null,
                ['request_uri' => 'api/v2/shop/countries'],
            ),
        );
    }
}
