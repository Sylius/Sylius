<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Grid\Definition;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Grid\Definition\Action;
use Sylius\Grid\Definition\ActionGroup;
use Sylius\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Grid\Definition\Field;
use Sylius\Grid\Definition\Filter;
use Sylius\Grid\Definition\Grid;

/**
 * @mixin ArrayToDefinitionConverter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArrayToDefinitionConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Grid\Definition\ArrayToDefinitionConverter');
    }
    
    function it_implements_array_to_definition_converter()
    {
        $this->shouldImplement(ArrayToDefinitionConverterInterface::class);
    }

    function it_converts_an_array_to_grid_definition()
    {
        $grid = Grid::fromCodeAndDriverConfiguration('sylius_admin_tax_category', 'doctrine/orm', ['resource' => 'sylius.tax_category']);

        $grid->setSorting(['name' => 'desc']);

        $codeField = Field::fromNameAndType('code', 'string');
        $codeField->setLabel('System Code');
        $codeField->setPath('method.code');
        $codeField->setOptions(['template' => 'bar.html.twig']);

        $grid->addField($codeField);

        $viewAction = Action::fromNameAndType('view', 'link');
        $viewAction->setLabel('Display Tax Category');
        $viewAction->setOptions(['foo' => 'bar']);
        $defaultActionGroup = ActionGroup::named('default');
        $defaultActionGroup->addAction($viewAction);

        $grid->addActionGroup($defaultActionGroup);

        $filter = Filter::fromNameAndType('enabled', 'boolean');
        $filter->setOptions(['fields' => ['firstName', 'lastName']]);
        $grid->addFilter($filter);

        $definitionArray = [
            'driver' => [
                'name' => 'doctrine/orm',
                'options' => ['resource' => 'sylius.tax_category'],
            ],
            'sorting' => [
                'name' => 'desc',
            ],
            'fields' => [
                'code' => [
                    'type' => 'string',
                    'label' => 'System Code',
                    'path' => 'method.code',
                    'options' => [
                        'template' => 'bar.html.twig'
                    ],
                ],
            ],
            'filters' => [
                'enabled' => [
                    'type' => 'boolean',
                    'options' => [
                        'fields' => ['firstName', 'lastName']
                    ]
                ]
            ],
            'actions' => [
                'default' => [
                    'view' => [
                        'type' => 'link',
                        'label' => 'Display Tax Category',
                        'options' => [
                            'foo' => 'bar',
                        ],
                    ]
                ]
            ]
        ];
        
        $this->convert('sylius_admin_tax_category', $definitionArray)->shouldBeSameGridAs($grid);
    }

    public function getMatchers()
    {
        return [
            'beSameGridAs' => function ($subject, $key) {
                return serialize($subject) === serialize($key);
            },
        ];
    }
}
