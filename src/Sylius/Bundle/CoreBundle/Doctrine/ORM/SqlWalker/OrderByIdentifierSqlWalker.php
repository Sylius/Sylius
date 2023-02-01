<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\SqlWalker;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\SqlWalker;

final class OrderByIdentifierSqlWalker extends SqlWalker
{
    public function walkSelectStatement(SelectStatement $AST)
    {
        $dqlAlias = $this->getDqlAlias();

        if (null !== $dqlAlias && $this->isOrderByIdentifierAllowed($AST)) {
            $this->appendOrderByIdentifier($AST, $dqlAlias);
        }

        return parent::walkSelectStatement($AST);
    }

    private function appendOrderByIdentifier(SelectStatement $ast, string $dqlAlias): void
    {
        $metadata = $this->getMetadataForDqlAlias($dqlAlias);

        $expression = new PathExpression(
            PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION,
            $dqlAlias,
            $metadata->getSingleIdentifierFieldName(),
        );
        $expression->type = PathExpression::TYPE_STATE_FIELD;

        $orderById = new OrderByItem($expression);
        $orderById->type = 'ASC';

        if (null === $ast->orderByClause) {
            $ast->orderByClause = new OrderByClause([$orderById]);

            return;
        }

        $ast->orderByClause->orderByItems[] = $orderById;
    }

    /**
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.13/cookbook/dql-custom-walkers.html#extending-dql-in-doctrine-orm-custom-ast-walkers
     */
    private function getDqlAlias(): ?string
    {
        /** @psalm-suppress UndefinedDocblockClass */
        foreach ($this->getQueryComponents() as $dqlAlias => $queryComponent) {
            if (
                isset($queryComponent['metadata']) &&
                null === ($queryComponent['parent'] ?? null) &&
                0 === ($queryComponent['nestingLevel'] ?? 0)
            ) {
                return $dqlAlias;
            }
        }

        return null;
    }

    private function isOrderByIdentifierAllowed(SelectStatement $ast): bool
    {
        // false if not sure the identifier is added to the GROUP BY clause
        if (null !== $ast->groupByClause) {
            return false;
        }

        $expression = current($ast->selectClause->selectExpressions)->expression;

        // false if aggregate functions is used
        return !$expression instanceof AggregateExpression && !$expression instanceof FunctionNode;
    }
}
