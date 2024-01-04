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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

final class NonChannelLocaleListenerSpec extends ObjectBehavior
{
    function let(
        RouterInterface $router,
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
    ): void {
        $this->beConstructedWith($router, $localeProvider, $firewallMap, ['shop']);
    }

    function it_throws_exception_on_instantiation_with_no_firewall_names(
        RouterInterface $router,
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
    ): void {
        $this->beConstructedWith($router, $localeProvider, $firewallMap, []);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_throws_exception_on_instantiation_with_non_string_firewall_names(
        RouterInterface $router,
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
    ): void {
        $this->beConstructedWith($router, $localeProvider, $firewallMap, [new \DateTime(), 1, 5.0]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_does_nothing_if_its_not_a_master_request(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(false);
        } else {
            $event->isMasterRequest()->willReturn(false);
        }

        $event->getRequest()->shouldNotBeCalled();
        $firewallMap->getFirewallConfig(Argument::any())->shouldNotBeCalled();
        $localeProvider->getAvailableLocalesCodes()->shouldNotBeCalled();

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_does_nothing_if_request_is_behind_no_firewall(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(null);

        $localeProvider->getAvailableLocalesCodes()->shouldNotBeCalled();

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_does_nothing_if_request_is_behind_a_firewall_not_stated_upon_creating(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('lalaland', 'mock'),
        );

        $localeProvider->getAvailableLocalesCodes()->shouldNotBeCalled();

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_does_nothing_if_request_locale_is_present_in_the_provider(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('shop', 'mock'),
        );

        $request->getLocale()->willReturn('en');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en', 'ga_IE']);

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_does_nothing_if_request_locale_is_not_present_in_provider_and_request_route_is_for_toolbar_or_profiler(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('shop', 'mock'),
        );

        $request->attributes = new ParameterBag(['_route' => '_wdt']);
        $request->getLocale()->willReturn('en');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['ga', 'ga_IE']);

        $request->attributes = new ParameterBag(['_route' => '_profiler']);
        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_redirect_to_default_locale_if_request_locale_is_not_present_in_provider(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        RequestEvent $event,
        Router $router,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('shop', 'mock'),
        );

        $request->getLocale()->willReturn('en');
        $request->attributes = new ParameterBag(['_locale' => 'en']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['ga', 'ga_IE']);
        $localeProvider->getDefaultLocaleCode()->willReturn('ga');

        $router->generate('sylius_shop_homepage', ['_locale' => 'ga'])->willReturn('/ga/');

        $this->restrictRequestLocale($event);
        $event->setResponse(new RedirectResponse('/ga/'))->shouldHaveBeenCalledOnce();
    }

    function it_does_nothing_if_request_attributes_does_not_have_locale(
        Request $request,
        RequestEvent $event,
    ): void {
        if (\method_exists(RequestEvent::class, 'isMainRequest')) {
            $event->isMainRequest()->willReturn(true);
        } else {
            $event->isMasterRequest()->willReturn(true);
        }
        $event->getRequest()->willReturn($request);

        $request->getLocale()->willReturn('en');
        $request->attributes = new ParameterBag();

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }
}
