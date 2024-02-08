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

namespace spec\Sylius\Bundle\ShopBundle\Router;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocaleStrippingRouterSpec extends ObjectBehavior
{
    function let(RouterInterface $decoratedRouter, LocaleContextInterface $localeContext): void
    {
        $this->beConstructedWith($decoratedRouter, $localeContext);
    }

    function it_is_a_symfony_router(): void
    {
        $this->shouldImplement(RouterInterface::class);
    }

    function it_is_warmable(): void
    {
        $this->shouldImplement(WarmableInterface::class);

        $this->warmUp('/cache/dir');
    }

    function it_delegates_path_info_mathing_to_inner_router(
        RouterInterface $decoratedRouter,
    ): void {
        $decoratedRouter->match('/path/info')->willReturn(['matched' => true]);

        $this->match('/path/info')->shouldReturn(['matched' => true]);
    }

    function it_delegates_request_matching_to_inner_router_path_info_matching_when_it_does_not_implement_request_matcher_interface(
        RouterInterface $decoratedRouter,
        Request $request,
    ): void {
        $request->getPathInfo()->willReturn('/path/info');
        $decoratedRouter->match('/path/info')->willReturn(['matched' => true]);

        $this->matchRequest($request)->shouldReturn(['matched' => true]);
    }

    function it_delegates_request_matching_to_inner_router(
        RouterInterface $router,
        LocaleContextInterface $localeContext,
        Request $request,
    ): void {
        $router->implement(RequestMatcherInterface::class);
        $this->beConstructedWith($router, $localeContext);

        $router->match(Argument::any())->shouldNotBeCalled();
        $router->matchRequest($request)->willReturn(['matched' => true]);

        $this->matchRequest($request)->shouldReturn(['matched' => true]);
    }

    function it_strips_locale_from_the_generated_url_if_locale_is_the_same_as_the_one_from_context(
        RouterInterface $decoratedRouter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willReturn('pl_PL');

        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn(
                'https://generated.url/?_locale=pl_PL',
                'https://generated.url/?foo=bar&_locale=pl_PL',
                'https://generated.url/?_locale=pl_PL&foo=bar',
                'https://generated.url/?bar=foo&_locale=pl_PL&foo=bar',
            )
        ;

        $this->generate('route_name')->shouldReturn('https://generated.url/');
        $this->generate('route_name')->shouldReturn('https://generated.url/?foo=bar');
        $this->generate('route_name')->shouldReturn('https://generated.url/?foo=bar');
        $this->generate('route_name')->shouldReturn('https://generated.url/?bar=foo&foo=bar');
    }

    function it_does_not_strip_locale_from_the_generated_url_if_locale_is_different_than_the_one_from_context(
        RouterInterface $decoratedRouter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willReturn('en_US');

        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('https://generated.url/?_locale=pl_PL')
        ;

        $this->generate('route_name')->shouldReturn('https://generated.url/?_locale=pl_PL');
    }

    function it_does_not_stirp_locale_from_the_generated_url_if_there_is_no_locale_parameter(
        RouterInterface $decoratedRouter,
    ): void {
        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('https://generated.url/')
        ;

        $this->generate('route_name')->shouldReturn('https://generated.url/');
    }
}
