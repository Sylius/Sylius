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

namespace Sylius\Bundle\ApiBundle\Tests\Filter\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Filter\Doctrine\TranslationOrderNameAndLocaleFilter;
use Sylius\Component\Core\Model\ProductInterface;

final class TranslationOrderNameAndLocaleFilterTest extends TestCase
{
    /** @var MockObject&QueryBuilder */
    private MockObject $queryBuilder;

    /** @var MockObject&QueryNameGeneratorInterface */
    private MockObject $queryNameGenerator;

    /** @var MockObject&EntityManagerInterface */
    private MockObject $entityManager;

    /** @var MockObject&ClassMetadata */
    private MockObject $classMetadata;

    protected function setUp(): void
    {
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);
    }

    /** @test */
    public function it_applies_ascending_order_by_translation_name(): void
    {
        $this->configureQueryBuilderWithTranslationsAssociation();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('translation.name', 'asc')
            ->willReturnSelf()
        ;

        $this->applyFilter('asc');
    }

    /** @test */
    public function it_applies_descending_order_by_translation_name(): void
    {
        $this->configureQueryBuilderWithTranslationsAssociation();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('translation.name', 'desc')
            ->willReturnSelf()
        ;

        $this->applyFilter('desc');
    }

    /** @test */
    public function it_applies_order_with_uppercase_direction(): void
    {
        $this->configureQueryBuilderWithTranslationsAssociation();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('translation.name', 'desc')
            ->willReturnSelf()
        ;

        $this->applyFilter('DESC');
    }

    /** @test */
    public function it_does_not_apply_order_with_invalid_direction(): void
    {
        $this->configureQueryBuilderWithTranslationsAssociation();

        $this->queryBuilder
            ->expects($this->never())
            ->method('orderBy')
        ;

        $this->applyFilter('INVALID');
    }

    /** @test */
    public function it_does_not_apply_order_with_dql_injection_payload(): void
    {
        $this->configureQueryBuilderWithTranslationsAssociation();

        $this->queryBuilder
            ->expects($this->never())
            ->method('orderBy')
        ;

        $this->applyFilter('ASC, variant.code DESC');
    }

    private function configureQueryBuilderWithTranslationsAssociation(): void
    {
        $this->queryBuilder
            ->method('getEntityManager')
            ->willReturn($this->entityManager)
        ;

        $this->queryBuilder
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;

        $this->entityManager
            ->method('getClassMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->classMetadata)
        ;

        $this->classMetadata
            ->method('hasAssociation')
            ->with('translations')
            ->willReturn(true)
        ;
    }

    private function applyFilter(string $direction): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $filter = new TranslationOrderNameAndLocaleFilter($managerRegistry);
        $filter->apply(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            null,
            [
                'filters' => ['order' => ['translation.name' => $direction]],
            ],
        );
    }
}
