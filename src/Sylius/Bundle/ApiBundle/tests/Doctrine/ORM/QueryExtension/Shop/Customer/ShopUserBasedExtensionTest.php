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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer\ShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Model\ResourceInterface;

final class ShopUserBasedExtensionTest extends TestCase
{
    private ShopUserBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $nameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->extension = new ShopUserBasedExtension($this->sectionProvider, $this->userContext);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->userContext->expects($this->never())->method('getUser');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, ResourceInterface::class, new Get());
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $section = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->expects($this->never())->method('getUser');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, CustomerInterface::class, new Get());
    }

    public function test_it_applies_conditions_to_collection(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $user = $this->createMock(ShopUserInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprComparison = $this->createMock(Expr\Comparison::class);

        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->method('getUser')->willReturn($user);

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->nameGenerator->method('generateJoinAlias')->with('user')->willReturn('user');
        $this->nameGenerator->method('generateParameterName')->with('user')->willReturn('user');
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('eq')->with('user.id', ':user')->willReturn($exprComparison);

        $this->queryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with('o.user', 'user', 'WITH', $exprComparison)
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('user', $user)
            ->willReturnSelf();

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, CustomerInterface::class, new Get());
    }

    public function test_it_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $this->userContext->expects($this->never())->method('getUser');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, ResourceInterface::class, [], new Get());
    }

    public function test_it_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $section = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->expects($this->never())->method('getUser');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, CustomerInterface::class, [], new Get());
    }

    public function test_it_applies_conditions_to_item(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprComparison = $this->createMock(Expr\Comparison::class);

        $user->method('getCustomer')->willReturn($customer);
        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->method('getUser')->willReturn($user);

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->nameGenerator->method('generateJoinAlias')->with('user')->willReturn('user');
        $this->nameGenerator->method('generateParameterName')->with('user')->willReturn('user');
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('eq')->with('user.id', ':user')->willReturn($exprComparison);

        $this->queryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with('o.user', 'user', 'WITH', $exprComparison)
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('user', $user)
            ->willReturnSelf();

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, CustomerInterface::class, [], new Get());
    }
}
