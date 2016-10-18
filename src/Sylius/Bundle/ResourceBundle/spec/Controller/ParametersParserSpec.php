<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Dosena Ishmael <nukboon@gmail.com>
 */
final class ParametersParserSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, ExpressionLanguage $expression)
    {
        $this->beConstructedWith($container, $expression);
    }

    function it_implements_parameters_parser_interface()
    {
        $this->shouldImplement(ParametersParserInterface::class);
    }

    function it_should_parse_parameters(Request $request)
    {
        $request->get('criteria')->willReturn('New criteria');
        $request->get('sorting')->willReturn('New sorting');

        $this->parseRequestValues(
            [
                'criteria' => '$criteria',
                'sortable' => '$sorting',
            ],
            $request
        )->shouldReturn(
            [
                'criteria' => 'New criteria',
                'sortable' => 'New sorting',
            ]
        );
    }

    function it_should_parse_complex_parameters(Request $request)
    {
        $request->get('enable')->willReturn(true);
        $request->get('sorting')->willReturn('New sorting');

        $this->parseRequestValues(
            [
                'criteria' => [
                    'enable' => '$enable',
                ],
                'sortable' => '$sorting',
            ],
            $request
        )->shouldReturn(
            [
                'criteria' => [
                    'enable' => true,
                ],
                'sortable' => 'New sorting',
            ]
        );
    }

    function it_should_parse_expression_with_parameters(
        ContainerInterface $container,
        ExpressionLanguage $expression,
        Request $request
    ) {
        $request->get('foo')->willReturn('bar');
        $request->get('baz')->willReturn(1);

        $expression->evaluate('service("demo_service")', ['container' => $container])->willReturn('demo_object');

        $this->parseRequestValues(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'expr:service("demo_service")',
                    ],
                ],
            ],
            $request
        )->shouldReturn(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'demo_object',
                    ],
                ],
            ]
        );

        $expression
            ->evaluate('service("demo_service")->getWith("bar")', ['container' => $container])
            ->willReturn('demo_object->getWith("bar")')
    ;

        $this->parseRequestValues(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'expr:service("demo_service")->getWith($foo)',
                    ],
                ],
            ],
            $request
        )->shouldReturn(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'demo_object->getWith("bar")',
                    ],
                ],
            ]
        );

        $expression
            ->evaluate('service("demo_service")->getWith("bar", 1)', ['container' => $container])
            ->willReturn('demo_object->getWith("bar", 1)')
        ;

        $this->parseRequestValues(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'expr:service("demo_service")->getWith($foo, $baz)',
                    ],
                ],
            ],
            $request
        )->shouldReturn(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'demo_object->getWith("bar", 1)',
                    ],
                ],
            ]
        );

        $expression
            ->evaluate('service("demo_service")->getWith("bar")->andGet(1)', ['container' => $container])
            ->willReturn('demo_object->getWith("bar")->andGet(1)')
        ;

        $this->parseRequestValues(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'expr:service("demo_service")->getWith($foo)->andGet($baz)',
                    ],
                ],
            ],
            $request
        )->shouldReturn(
            [
                'factory' => [
                    'method' => 'createByParameter',
                    'arguments' => [
                        'demo_object->getWith("bar")->andGet(1)',
                    ],
                ],
            ]
        );
    }
}
