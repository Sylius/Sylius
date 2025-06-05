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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\EnabledExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class EnabledExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private EnabledExtension $enabledExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->enabledExtension = new EnabledExtension($this->sectionProviderMock);
    }

    public function testFiltersEnabledPaymentMethod(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('enabled')->willReturn('enabled');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $this->enabledExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            PaymentMethodInterface::class,
            [],
            new Get(),
        );
    }

    public function testFiltersEnabledPaymentMethods(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('enabled')->willReturn('enabled');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.enabled = :enabled')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('enabled', true)->willReturn($queryBuilderMock);
        $this->enabledExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            PaymentMethodInterface::class,
            new GetCollection(),
        );
    }

    public function testDoesNothingIfTheCurrentResourceIsNotAPaymentMethodForItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->enabledExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, [], new Get());
    }

    public function testDoesNothingIfTheCurrentResourceIsNotAPaymentMethodForCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->enabledExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, new GetCollection());
    }

    public function testDoesNothingIfTheCurrentSectionIsNotAShopForItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->enabledExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ShippingMethodInterface::class, [], new Get());
    }

    public function testDoesNothingIfTheCurrentSectionIsNotAShopForCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->enabledExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ShippingMethodInterface::class, new GetCollection());
    }
}
