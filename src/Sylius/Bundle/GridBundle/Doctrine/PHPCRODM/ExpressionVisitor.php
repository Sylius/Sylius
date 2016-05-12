<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ODM\PHPCR\Query\Builder\AbstractNode;
use Doctrine\ODM\PHPCR\Query\Builder\OperandFactory;
use Doctrine\Common\Collections\Expr\Expression;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison;

/**
 * Walks a Doctrine\Commons\Expr object graph and builds up a PHPCR-ODM
 * query using the (fluent) PHPCR-ODM query builder.
 */
class ExpressionVisitor
{
    private $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function walkComparison(Comparison $comparison, AbstractNode $parentNode)
    {
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue(); // shortcut for walkValue()

        switch ($comparison->getOperator()) {
            case Comparison::EQ:
                return $parentNode->eq()->field($this->getField($field))->literal($value)->end();

            case Comparison::NEQ:
                return $parentNode->neq()->field($this->getField($field))->literal($value)->end();

            case Comparison::LT:
                return $parentNode->lt()->field($this->getField($field))->literal($value)->end();

            case Comparison::LTE:
                return $parentNode->lte()->field($this->getField($field))->literal($value)->end();

            case Comparison::GT:
                return $parentNode->gt()->field($this->getField($field))->literal($value)->end();

            case Comparison::GTE:
                return $parentNode->gte()->field($this->getField($field))->literal($value)->end();

            case Comparison::IN:
                return $this->getInConstraint($parentNode, $field, $value);

            case Comparison::NIN:
                $node = $parentNode->not();
                $this->getInConstraint($node, $field, $value);
                return $node->end();

            case Comparison::CONTAINS:
                return $parentNode->like()->field($this->getField($field))->literal($value)->end();

            case ExtraComparison::NOT_CONTAINS:
                return $parentNode->not()->like()->field($this->getField($field))->literal($value)->end()->end();

            case ExtraComparison::IS_NULL:
                return $parentNode->not()->fieldIsset($this->getField($field))->end();

            case ExtraComparison::IS_NOT_NULL:
                return $parentNode->fieldIsset($this->getField($field));

            default:
                throw new \RuntimeException("Unknown comparison operator: " . $comparison->getOperator());
        }

        return $parentNode;
    }

    /**
     * {@inheritDoc}
     */
    public function walkValue(Value $value, OperandFactory $parentNode)
    {
        throw new \BadMethodCallException('What is this even used for?');
        return $parentNode->literal($value->getValue());
    }

    /**
     * {@inheritDoc}
     */
    public function walkCompositeExpression(CompositeExpression $expr, AbstractNode $parentNode)
    {
        switch($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                $node = $parentNode->andX();
                break;
            case CompositeExpression::TYPE_OR:
                $node = $parentNode->orX();
                break;
            default:
                throw new \RuntimeException('Unknown composite: ' . $expr->getType());
        }

        $expressions = $expr->getExpressionList();

        $leftExpression = array_shift($expressions);
        $this->dispatch($leftExpression, $node);

        $parentNode = $node;
        foreach ($expressions as $index => $expression) {
            if (count($expressions) === $index + 1) {
                $this->dispatch($expression, $parentNode);
                break;
            }

            switch($expr->getType()) {
                case CompositeExpression::TYPE_AND:
                    $parentNode = $parentNode->andX();
                    break;
                case CompositeExpression::TYPE_OR:
                    $parentNode = $parentNode->orX();
                    break;
            }

            $this->dispatch($expression, $parentNode);
        }

        return $node;
    }

    /**
     * Walk the given expression to build up the PHPCR-ODM query builder.
     *
     * @param Expression $expr
     * @param AbstractNode $parentNode
     */
    public function dispatch(Expression $expr, AbstractNode $parentNode = null)
    {
        if ($parentNode === null) {
            $parentNode = $this->queryBuilder->where();
        }

        switch (true) {
            case ($expr instanceof Comparison):
                return $this->walkComparison($expr, $parentNode);

            case ($expr instanceof Value):
                return $this->walkValue($expr, $parentNode);

            case ($expr instanceof CompositeExpression):
                return $this->walkCompositeExpression($expr, $parentNode);

            default:
                throw new \RuntimeException("Unknown Expression " . get_class($expr));
        }
    }

    private function getField($field)
    {
        return Driver::QB_SOURCE_ALIAS . '.' . $field;
    }

    private function getInConstraint(AbstractNode $parentNode, $field, array $values)
    {
        $orNode = $parentNode->orx();

        foreach ($values as $value) {
            $orNode->eq()->field($this->getField($field))->literal($value);
        }

        $orNode->end();
    }
}
