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

namespace spec\Sylius\Bundle\ResourceBundle\Grid\Parser;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParser;
use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OptionsParserSpec extends ObjectBehavior
{
    function let(
        ContainerInterface $container,
        ExpressionLanguage $expression,
        PropertyAccessorInterface $propertyAccessor
    ): void {
        $this->beConstructedWith($container, $expression, $propertyAccessor);
    }

    function it_is_an_options_parser(): void
    {
        $this->shouldImplement(OptionsParserInterface::class);
    }

    function it_parses_options(Request $request): void
    {
        $request->get('id')->willReturn(7);

        $this
            ->parseOptions(['id' => '$id'], $request)
            ->shouldReturn(['id' => 7])
        ;
    }

    function it_parses_options_with_expression(
        ContainerInterface $container,
        ExpressionLanguage $expression,
        Request $request
    ): void {
        $expression->evaluate('service("demo_service")', ['container' => $container])->willReturn('demo_object');

        $this
            ->parseOptions(
                [
                    'factory' => [
                        'method' => 'createByParameter',
                        'arguments' => [
                            'expr:service("demo_service")',
                        ],
                    ],
                ],
                $request
            )
            ->shouldReturn(
                [
                    'factory' => [
                        'method' => 'createByParameter',
                        'arguments' => [
                            'demo_object',
                        ],
                    ],
                ]
        );
    }

    function it_parses_options_with_parameter_from_resource(
        PropertyAccessorInterface $propertyAccessor,
        Request $request,
        ResourceInterface $data
    ): void {
        $propertyAccessor->getValue($data, 'id')->willReturn(21);

        $this
            ->parseOptions(['id' => 'resource.id'], $request, $data)
            ->shouldReturn(['id' => 21])
        ;
    }
}
