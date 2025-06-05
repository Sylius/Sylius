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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\NonArchivedExtension;
use Sylius\Resource\Model\ArchivableInterface;
use Symfony\Component\HttpFoundation\Request;

final class NonArchivedExtensionTest extends TestCase
{
    private NonArchivedExtension $nonArchivedExtension;

    protected function setUp(): void
    {
        $this->nonArchivedExtension = new NonArchivedExtension();
    }

    public function testDoesNothingIfCurrentResourceIsNotInstanceOfArchivableInterface(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->nonArchivedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            stdClass::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function testDoesNothingIfArchivedAtFilterIsAlreadyApplied(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $this->nonArchivedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ArchivableInterface::class,
            new Get(name: Request::METHOD_GET),
            ['filters' => ['exists' => ['archivedAt' => 'true']]],
        );
    }

    public function testAppliesConditionsToCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $exprMock->expects(self::once())->method('isNull')->with('o.archivedAt')->willReturn('o.archivedAt IS NULL');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.archivedAt IS NULL')->willReturn($queryBuilderMock);
        $this->nonArchivedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            ArchivableInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }
}
