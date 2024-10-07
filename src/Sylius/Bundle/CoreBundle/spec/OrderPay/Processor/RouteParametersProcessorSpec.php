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

namespace spec\Sylius\Bundle\CoreBundle\OrderPay\Processor;

use PhpSpec\ObjectBehavior;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\RouterInterface;

final class RouteParametersProcessorSpec extends ObjectBehavior
{
    function let(
        ExpressionLanguage $expressionLanguage,
        RouterInterface $router,
    ): void {
        $this->beConstructedWith(
            $expressionLanguage,
            $router,
        );
    }

    function it_processes_from_a_route(
        RouterInterface $router,
    ): void {
        $router->generate('a_route', [])->willReturn('https://localhost/a_route');

        $this->process('a_route')->shouldReturn('https://localhost/a_route');
    }

    function it_processes_from_a_route_and_parameters(
        RouterInterface $router,
        ExpressionLanguage $expressionLanguage,
    ): void {
        $expressionLanguage->evaluate('value', [])->willReturn('value')->shouldBeCalledOnce();

        $router->generate(
            'a_route',
            ['aParam'=>'value'],
        )->willReturn('https://localhost/a_route?aParam=value');

        $this->process(
            'a_route',
            ['aParam' => 'value'],
        )->shouldReturn('https://localhost/a_route?aParam=value');
    }

    function it_processes_from_a_route_and_parameters_and_context(
        RouterInterface $router,
        ExpressionLanguage $expressionLanguage,
    ): void {
        $expressionLanguage->evaluate('value', [
            'value' => '1'
        ])->willReturn('1')->shouldBeCalledOnce();

        $router->generate(
            'a_route',
            ['aParam'=>'1'],
        )->willReturn('https://localhost/a_route?aParam=1');

        $this->process(
            'a_route',
            ['aParam' => 'value'],
            ['value' => '1'],
        )->shouldReturn('https://localhost/a_route?aParam=1');
    }
}
