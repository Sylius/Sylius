<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Tests\DependencyInjection;

use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ODM\PHPCR\Query\Builder\ConverterInterface;
use Doctrine\ODM\PHPCR\Query\Builder\OperandStaticLiteral;
use Doctrine\ODM\PHPCR\Query\Builder\OperandDynamicField;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\AbstractNode;
use Doctrine\ODM\PHPCR\Query\Builder\AbstractLeafNode;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintFieldIsset;

/**
 * Creates a string representation of any given PHPCR-ODM QueryBuilder
 * node in order that the tests can clearly assert the state of it.
 */
class QueryBuilderWalker
{
    /**
     * Create a string representation of the given query builder node.
     *
     * @param AbstractNode $node
     *
     * @return string
     */
    public function toString(AbstractNode $node)
    {
        return implode(' ', $this->walk($node));
    }

    private function walk(AbstractNode $node, $elements = [])
    {
        $elements[] = $this->stringValue($node);

        if ($node instanceof AbstractLeafNode) {
            return $elements;
        }

        $elements[] = '(';

        foreach ($node->getChildren() as $childNode) {
            $elements = $this->walk($childNode, $elements);
        }

        $elements[] = ')';

        return $elements;
    }

    private function stringValue(AbstractNode $node)
    {
        $refl = new \ReflectionClass(get_class($node));
        $nodeName = $refl->getShortName();
        if ($node instanceof ConstraintComparison) {
            return sprintf('%s', $node->getOperator());
        }

        if ($node instanceof OperandDynamicField) {
            return sprintf('%s(%s.%s)', $nodeName, $node->getAlias(), $node->getField());
        }

        if ($node instanceof OperandStaticLiteral) {
            return sprintf('%s("%s")', $nodeName, $node->getValue());
        }

        if ($node instanceof ConstraintFieldIsset) {
            return sprintf('%s("%s")', $nodeName, $node->getField());
        }

        return $nodeName;
    }
}
