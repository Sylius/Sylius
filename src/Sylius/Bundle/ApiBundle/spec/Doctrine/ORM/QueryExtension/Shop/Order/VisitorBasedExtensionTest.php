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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\VisitorBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Model\ResourceInterface;

final class VisitorBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    /** @var UserContextInterface|MockObject */
    private MockObject $userContextMock;

    private VisitorBasedExtension $visitorBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->userContextMock = $this->createMock(UserContextInterface::class);
        $this->visitorBasedExtension = new VisitorBasedExtension($this->sectionProviderMock, $this->userContextMock);
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->visitorBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ResourceInterface::class, [], new Get());
    }

    public function testDoesNotApplyConditionsToItemForAdminApiSection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var AdminApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->visitorBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], new Get());
    }

    public function testDoesNotApplyConditionsToItemIfUserIsNotNull(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($userMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->visitorBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], new Get());
    }

    public function testAppliesConditionsToItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprEqMock */
        $exprEqMock = $this->createMock(Comparison::class);
        /** @var Andx|MockObject $exprAndxMock */
        $exprAndxMock = $this->createMock(Andx::class);
        /** @var Orx|MockObject $exprOrxMock */
        $exprOrxMock = $this->createMock(Orx::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn(null);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects($this->exactly(2))->method('generateJoinAlias')->willReturnMap([['customer', 'customer'], ['user', 'user']]);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('createdByGuest')->willReturn('createdByGuest');
        $queryBuilderMock->expects(self::once())->method('leftJoin')->with('o.customer', 'customer')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('leftJoin')->willReturnMap([['o.customer', 'customer', $queryBuilderMock], ['customer.user', 'user', $queryBuilderMock]]);
        $exprMock->expects(self::once())->method('isNull')->with('user')->willReturn('user IS NULL');
        $exprMock->expects(self::once())->method('isNull')->with('o.customer')->willReturn('o.customer IS NULL');
        $exprMock->expects($this->exactly(2))->method('isNull')->willReturnMap([['user', 'user IS NULL'], ['o.customer', 'o.customer IS NULL']]);
        $exprMock->expects(self::once())->method('andX')->with('user IS NOT NULL', $exprEqMock)->willReturn($exprAndxMock);
        $exprMock->expects(self::once())->method('orX')->with('user IS NULL', 'o.customer IS NULL', $exprAndxMock)->willReturn($exprOrxMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprOrxMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('createdByGuest', true)->willReturn($queryBuilderMock);
        $this->visitorBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], new Get());
    }
}
