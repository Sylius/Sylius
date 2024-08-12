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

namespace Sylius\Bundle\ApiBundle\Tests\Doctrine\QueryCollectionExtension;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\TranslationOrderLocaleExtension;
use Sylius\Component\Core\Model\ProductInterface;

final class TranslationOrderLocaleExtensionTest extends TestCase
{
    private QueryNameGeneratorInterface&MockObject $queryNameGenerator;

    private QueryBuilder&MockObject $queryBuilder;

    private EntityManagerInterface&MockObject $entityManager;

    private ClassMetadata&MockObject $classMetadata;

    protected function setUp(): void
    {
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);
    }

    /** @test */
    public function it_does_nothing_when_resource_class_is_not_translatable(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('leftJoin')
        ;

        $this->doApplyToCollection(\stdClass::class);
    }

    /** @test */
    public function it_does_nothing_when_the_resource_is_not_sorted_by_translation_name(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('leftJoin')
        ;

        $this->doApplyToCollection(ProductInterface::class);
    }

    /** @test */
    public function it_does_nothing_when_the_resource_does_not_have_a_translations_association(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->classMetadata)
        ;

        $this->classMetadata
            ->expects($this->once())
            ->method('hasAssociation')
            ->with('translations')
            ->willReturn(false)
        ;

        $this->queryBuilder
            ->expects($this->never())
            ->method('leftJoin')
        ;

        $this->doApplyToCollection(ProductInterface::class, [
            'filters' => [
                'order' => [
                    'translation.name' => 'test',
                ],
            ],
        ]);
    }

    /** @test */
    public function it_does_nothing_when_no_locale_code_has_been_resolved_from_filters(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->classMetadata)
        ;

        $this->classMetadata
            ->expects($this->once())
            ->method('hasAssociation')
            ->with('translations')
            ->willReturn(true)
        ;

        $this->queryBuilder
            ->expects($this->never())
            ->method('leftJoin')
            ->withAnyParameters()
        ;

        $this->doApplyToCollection(ProductInterface::class, [
            'filters' => [
                'order' => [
                    'translation.name' => 'test',
                ],
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider getLocaleCodeContexts
     */
    public function it_joins_on_a_specific_translation_when_locale_code_has_been_resolved_from_filters(
        array $contextWithLocaleCode,
    ): void {
        $this->queryBuilder
            ->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with(ProductInterface::class)
            ->willReturn($this->classMetadata)
        ;

        $this->classMetadata
            ->expects($this->once())
            ->method('hasAssociation')
            ->with('translations')
            ->willReturn(true)
        ;

        $this->queryBuilder
            ->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['alias'])
        ;

        $this->queryNameGenerator
            ->method('generateParameterName')
            ->with('localeCode')
            ->willReturn('param')
        ;

        $this->queryBuilder
            ->expects($this->once())
            ->method('addSelect')
            ->with('translation')
            ->willReturnSelf()
        ;

        $this->queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with('alias.translations', 'translation', Join::WITH, 'translation.locale = :param')
            ->willReturnSelf()
        ;

        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('param', 'en_US')
            ->willReturnSelf()
        ;

        $this->doApplyToCollection(ProductInterface::class, array_merge_recursive([
            'filters' => [
                'order' => [
                    'translation.name' => 'test',
                ],
            ],
        ], $contextWithLocaleCode));
    }

    /** @param array<string, mixed> $context */
    private function doApplyToCollection(string $resourceClass, array $context = []): void
    {
        (new TranslationOrderLocaleExtension())->applyToCollection(
            queryBuilder: $this->queryBuilder,
            queryNameGenerator: $this->queryNameGenerator,
            resourceClass: $resourceClass,
            context: $context,
        );
    }

    /** @return iterable<array<string, mixed>> */
    private function getLocaleCodeContexts(): iterable
    {
        yield 'locale code in documentation filter' => [[
            'filters' => [
                'localeCode for order' => ['translation.name' => 'en_US'],
            ],
        ]];
        yield 'locale code in localeCode translation.name filter' => [[
            'filters' => [
                'order' => ['localeCode' => ['translation.name' => 'en_US']],
            ],
        ]];
        yield 'locale code in localeCode filter' => [[
            'filters' => [
                'order' => ['localeCode' => 'en_US'],
            ],
        ]];
    }
}
