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

namespace spec\Sylius\Bundle\ShopBundle\Router;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    function it_strips_locale_from_the_generated_url_if_locale_is_the_same_as_the_one_from_context(
        RouterInterface $decoratedRouter,
        LocaleContextInterface $localeContext
    ): void {
        $localeContext->getLocaleCode()->willReturn('pl_PL');

        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn(
                'http://generated.url/?_locale=pl_PL',
                'http://generated.url/?foo=bar&_locale=pl_PL',
                'http://generated.url/?_locale=pl_PL&foo=bar',
                'http://generated.url/?bar=foo&_locale=pl_PL&foo=bar'
            )
        ;

        $this->generate('route_name')->shouldReturn('http://generated.url/');
        $this->generate('route_name')->shouldReturn('http://generated.url/?foo=bar');
        $this->generate('route_name')->shouldReturn('http://generated.url/?foo=bar');
        $this->generate('route_name')->shouldReturn('http://generated.url/?bar=foo&foo=bar');
    }

    function it_does_not_strip_locale_from_the_generated_url_if_locale_is_different_than_the_one_from_context(
        RouterInterface $decoratedRouter,
        LocaleContextInterface $localeContext
    ): void {
        $localeContext->getLocaleCode()->willReturn('en_US');

        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('http://generated.url/?_locale=pl_PL')
        ;

        $this->generate('route_name')->shouldReturn('http://generated.url/?_locale=pl_PL');
    }

    function it_does_not_stirp_locale_from_the_generated_url_if_there_is_no_locale_parameter(
        RouterInterface $decoratedRouter
    ): void {
        $decoratedRouter
            ->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('http://generated.url/')
        ;

        $this->generate('route_name')->shouldReturn('http://generated.url/');
    }
}
