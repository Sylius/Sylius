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

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Dosena Ishmael <nukboon@gmail.com>
 */
final class ParametersParserSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(new Container(), new ExpressionLanguage());
    }

    function it_implements_parameters_parser_interface(): void
    {
        $this->shouldImplement(ParametersParserInterface::class);
    }

    function it_parses_string_parameters(): void
    {
        $request = new Request();
        $request->request->set('string', 'Lorem ipsum');

        $this
            ->parseRequestValues(['nested' => ['string' => '$string']], $request)
            ->shouldReturn(['nested' => ['string' => 'Lorem ipsum']])
        ;
    }

    function it_parses_boolean_parameters(): void
    {
        $request = new Request();
        $request->request->set('boolean', true);

        $this
            ->parseRequestValues(['nested' => ['boolean' => '$boolean']], $request)
            ->shouldReturn(['nested' => ['boolean' => true]])
        ;
    }

    function it_parses_array_parameters(): void
    {
        $request = new Request();
        $request->request->set('array', ['foo' => 'bar']);

        $this
            ->parseRequestValues(['nested' => ['array' => '$array']], $request)
            ->shouldReturn(['nested' => ['array' => ['foo' => 'bar']]])
        ;
    }

    function it_parser_string_parameter_and_change_its_type_to_int(): void
    {
        $request = new Request();
        $request->request->set('int', '5');

        $this
            ->parseRequestValues(['nested' => ['int' => '!!int $int']], $request)
            ->shouldReturn(['nested' => ['int' => 5]])
        ;
    }

    function it_parser_string_parameter_and_change_its_type_to_float(): void
    {
        $request = new Request();
        $request->request->set('float', '5.4');

        $this
            ->parseRequestValues(['nested' => ['float' => '!!float $float']], $request)
            ->shouldReturn(['nested' => ['float' => 5.4]])
        ;
    }

    function it_parser_string_parameter_and_change_its_type_to_double(): void
    {
        $request = new Request();
        $request->request->set('double', '5.4');

        $this
            ->parseRequestValues(['nested' => ['double' => '!!double $double']], $request)
            ->shouldReturn(['nested' => ['double' => 5.4]])
        ;
    }

    function it_throws_exception_if_string_parameter_is_going_to_be_parsed_to_invalid_type()
    {
        $request = new Request();
        $request->request->set('int', 5);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('parseRequestValues', [['nested' => ['int' => '!!invalid $int']], $request])
        ;
    }

    function it_parses_expressions(): void
    {
        $request = new Request();

        $this
            ->parseRequestValues(['nested' => ['boolean' => 'expr:"foo" in ["foo", "bar"]']], $request)
            ->shouldReturn(['nested' => ['boolean' => true]])
        ;
    }

    function it_parses_expressions_with_string_parameters(): void
    {
        $request = new Request();
        $request->request->set('string', 'lorem ipsum');

        $this
            ->parseRequestValues(['expression' => 'expr:$string === "lorem ipsum"'], $request)
            ->shouldReturn(['expression' => true])
        ;
    }

    function it_parses_expressions_with_scalar_parameters(): void
    {
        $request = new Request();
        $request->request->set('number', 6);

        $this
            ->parseRequestValues(['expression' => 'expr:$number === 6'], $request)
            ->shouldReturn(['expression' => true])
        ;
    }

    function it_throws_an_exception_if_array_parameter_is_injected_into_expression(): void
    {
        $request = new Request();
        $request->request->set('array', ['foo', 'bar']);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('parseRequestValues', [['expression' => 'expr:"foo" in $array'], $request])
        ;
    }

    function it_throws_an_exception_if_object_parameter_is_injected_into_expression(): void
    {
        $request = new Request();
        $request->request->set('object', new \stdClass());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('parseRequestValues', [['expression' => 'expr:$object.callMethod()'], $request])
        ;
    }
}
