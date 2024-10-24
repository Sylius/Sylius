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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Resource\Model\ArchivableInterface;
use Symfony\Component\HttpFoundation\Request;

final class NonArchivedExtensionSpec extends ObjectBehavior
{
    function it_does_nothing_if_current_resource_is_not_instance_of_archivable_interface(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            \stdClass::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    function it_does_nothing_if_archived_at_filter_is_already_applied(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ArchivableInterface::class,
            new Get(name: Request::METHOD_GET),
            ['filters' => ['exists' => ['archivedAt' => 'true']]],
        );
    }

    function it_applies_conditions_to_collection(
        QueryBuilder $queryBuilder,
        Expr $expr,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $expr->isNull('o.archivedAt')->willReturn('o.archivedAt IS NULL');
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->andWhere('o.archivedAt IS NULL')->shouldBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ArchivableInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }
}
