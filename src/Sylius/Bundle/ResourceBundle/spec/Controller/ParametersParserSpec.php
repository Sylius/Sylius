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
use Sylius\Bundle\ResourceBundle\Controller\ParametersParserInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Dosena Ishmael <nukboon@gmail.com>
 */
final class ParametersParserSpec extends ObjectBehavior
{
    function it_implements_parameters_parser_interface()
    {
        $this->beConstructedWith(new Container(), new ExpressionLanguage());
        $this->shouldImplement(ParametersParserInterface::class);
    }

    function it_should_parse_string_parameters()
    {
        $request = new Request();
        $request->request->set('string', 'Lorem ipsum');

        $this->beConstructedWith(new Container(), new ExpressionLanguage());

        $this
            ->parseRequestValues(['nested' => ['string' => '$string']], $request)
            ->shouldReturn(['nested' => ['string' => 'Lorem ipsum']])
        ;
    }

    function it_should_parse_boolean_parameters()
    {
        $request = new Request();
        $request->request->set('boolean', true);

        $this->beConstructedWith(new Container(), new ExpressionLanguage());

        $this
            ->parseRequestValues(['nested' => ['boolean' => '$boolean']], $request)
            ->shouldReturn(['nested' => ['boolean' => true]])
        ;
    }

    function it_should_parse_array_parameters()
    {
        $request = new Request();
        $request->request->set('array', ['foo' => 'bar']);

        $this->beConstructedWith(new Container(), new ExpressionLanguage());

        $this
            ->parseRequestValues(['nested' => ['array' => '$array']], $request)
            ->shouldReturn(['nested' => ['array' => ['foo' => 'bar']]])
        ;
    }

    function it_should_parse_expressions()
    {
        $service = new \stdClass();

        $container = new Container();
        $container->set('service', $service);

        $expression = new ExpressionLanguage();

        $request = new Request();

        $this->beConstructedWith($container, $expression);

        $this
            ->parseRequestValues(['nested' => ['service' => 'expr:service("service")']], $request)
            ->shouldReturn(['nested' => ['service' => $service]])
        ;
    }

    function it_should_parse_expressions_with_parameters()
    {
        $service = new \stdClass();

        $container = new Container();
        $container->set('service', $service);

        $expression = new ExpressionLanguage();

        $request = new Request();
        $request->request->set('serviceName', 'service');

        $this->beConstructedWith($container, $expression);

        $this
            ->parseRequestValues(['nested' => ['service' => 'expr:service($serviceName)']], $request)
            ->shouldReturn(['nested' => ['service' => $service]])
        ;
    }
}
