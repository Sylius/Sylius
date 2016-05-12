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
use Doctrine\ODM\PHPCR\Query\Builder\AbstractNode;
use Doctrine\ODM\PHPCR\Query\Builder\AbstractLeafNode;
use Doctrine\ODM\PHPCR\Query\Builder\OperandDynamicField;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\OperandStaticLiteral;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison;

require(__DIR__ . '/QueryBuilderWalker.php');

class ExpressionVisitorTest extends \PHPUnit_Framework_TestCase
{
    private $queryBuilder;
    private $visitor;

    public function setUp()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->visitor = new ExpressionVisitor($this->queryBuilder);
    }

    /**
     * @dataProvider provideComparisons
     */
    public function test_it_should_handle_comparisons($comparator, $expected)
    {
        $expr = new Comparison('hello', $comparator, 'world');
        $this->visitor->dispatch($expr);

        $this->assertQueryBuilderState($expected);
    }

    public function provideComparisons()
    {
        return [
            [
                Comparison::EQ,
                'jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::NEQ,
                'jcr.operator.not.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::LT,
                'jcr.operator.less.than ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::LTE,
                'jcr.operator.less.than.or.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::GT,
                'jcr.operator.greater.than ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::GTE,
                'jcr.operator.greater.than.or.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                Comparison::CONTAINS,
                'jcr.operator.like ( OperandDynamicField(o.hello) OperandStaticLiteral("world") )',
            ],
            [
                ExtraComparison::NOT_CONTAINS,
                'ConstraintNot ( jcr.operator.like ( OperandDynamicField(o.hello) OperandStaticLiteral("world") ) )',
            ],
            [
                ExtraComparison::IS_NULL,
                'ConstraintNot ( ConstraintFieldIsset("hello") )'
            ],
            [
                ExtraComparison::IS_NOT_NULL,
                'ConstraintFieldIsset("hello")'
            ],
        ];
    }

    /**
     * @dataProvider provideEmulateIn
     */
    public function test_it_should_emulate_in($comparator, array $values, $expected)
    {
        $expr = new Comparison('hello', $comparator, $values);
        $this->visitor->dispatch($expr);

        $this->assertQueryBuilderState($expected);
    }

    public function provideEmulateIn()
    {
        return [
            [
                Comparison::IN,
                [ 'one' ],
                'ConstraintOrx ( jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("one") ) )'
            ],
            [
                Comparison::IN,
                [ 'one', 'two', 'three' ],
                'ConstraintOrx ( jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("one") ) jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("two") ) jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("three") ) )',
            ],
            [
                Comparison::NIN,
                [ 'one' ],
                'ConstraintNot ( ConstraintOrx ( jcr.operator.equal.to ( OperandDynamicField(o.hello) OperandStaticLiteral("one") ) ) )'
            ],
        ];
    }

    /**
     * @dataProvider provideComposite
     */
    public function test_it_should_handle_a_composite_with_an_arity_of_2($type, $expectedType)
    {
        $expr1 = new Comparison('hello', Comparison::EQ, 'world');
        $expr2 = new Comparison('number', Comparison::GT, 8);
        $expr = new CompositeExpression(
            $type,
            [ $expr1, $expr2 ]
        );

        $this->visitor->dispatch($expr);
        $this->assertQueryBuilderState(<<<EOT
$expectedType ( 
    jcr.operator.equal.to (
        OperandDynamicField(o.hello) OperandStaticLiteral("world") 
    ) 
    jcr.operator.greater.than ( 
        OperandDynamicField(o.number) OperandStaticLiteral("8") 
    ) 
)
EOT
        );
    }

    public function provideComposite()
    {
        return [
            [ CompositeExpression::TYPE_AND, 'ConstraintAndx' ],
            [ CompositeExpression::TYPE_OR, 'ConstraintOrx' ]
        ];
    }

    public function test_it_should_handle_a_composite_with_an_arity_of_3()
    {
        $type = CompositeExpression::TYPE_AND;
        $expr1 = new Comparison('hello', Comparison::EQ, 'world');
        $expr2 = new Comparison('number', Comparison::GT, 8);
        $expr3 = new Comparison('date', Comparison::GT, '2015-12-10');
        $expr = new CompositeExpression(
            $type,
            [ $expr1, $expr2, $expr3 ]
        );

        $this->visitor->dispatch($expr);
        $this->assertQueryBuilderState(<<<'EOT'
ConstraintAndx ( 
    jcr.operator.equal.to ( 
        OperandDynamicField(o.hello) OperandStaticLiteral("world") 
    ) 
    ConstraintAndx ( 
        jcr.operator.greater.than ( 
            OperandDynamicField(o.number) OperandStaticLiteral("8") 
        ) 
        jcr.operator.greater.than ( 
            OperandDynamicField(o.date) OperandStaticLiteral("2015-12-10")
        )
    ) 
)
EOT
        );
    }

    public function test_it_should_handle_a_nested_composites()
    {
        $type = CompositeExpression::TYPE_AND;
        $expr1 = new Comparison('hello', Comparison::EQ, 'world');
        $expr2 = new Comparison('number', Comparison::GT, 8);
        $expr3 = new Comparison('date', Comparison::GT, '2015-12-10');
        $expr4 = new Comparison('date', Comparison::LT, '2016-12-10');
        $expr5 = new Comparison('date', Comparison::NEQ, '2016-12-10');

        $comp2 = new CompositeExpression(CompositeExpression::TYPE_AND, [ $expr2, $expr5 ]);
        $comp1 = new CompositeExpression(CompositeExpression::TYPE_OR, [ $expr1, $comp2 ]);

        $expr = new CompositeExpression(
            $type,
            [ $comp1, $expr3, $expr4 ]
        );

        $this->visitor->dispatch($expr);
        $this->assertQueryBuilderState(<<<EOT
ConstraintAndx ( 
    ConstraintOrx ( 
        jcr.operator.equal.to ( 
            OperandDynamicField(o.hello) OperandStaticLiteral("world") 
        ) 
        ConstraintAndx ( 
            jcr.operator.greater.than ( 
                OperandDynamicField(o.number) OperandStaticLiteral("8") 
            ) 
            jcr.operator.not.equal.to ( 
                OperandDynamicField(o.date) OperandStaticLiteral("2016-12-10")
            )
        ) 
    )
    ConstraintAndx ( 
        jcr.operator.greater.than ( 
            OperandDynamicField(o.date) OperandStaticLiteral("2015-12-10")
        )
        jcr.operator.less.than ( 
            OperandDynamicField(o.date) OperandStaticLiteral("2016-12-10")
        )
    ) 
)
EOT
        );
    }

    private function assertQueryBuilderState($expected)
    {
        $converter = new QueryBuilderWalker();
        $result = $converter->toString($this->queryBuilder->getChild('where')->getChild());
        $expected = preg_replace('{\s{2,}}', ' ', $expected);
        $expected = str_replace("\n", ' ', $expected);

        $this->assertEquals($expected, $result);
    }
}
