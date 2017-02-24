<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ShopBundle\EventListener\NonChannelLocaleListener;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class NonChannelLocaleListenerSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider, FirewallMap $firewallMap)
    {
        $this->beConstructedWith($localeProvider, $firewallMap, ['shop']);
    }

    function it_throws_exception_on_instantiation_with_no_firewall_names(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap
    ) {
        $this->beConstructedWith($localeProvider, $firewallMap, []);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_throws_exception_on_instantiation_with_non_string_firewall_names(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap
    ) {
        $this->beConstructedWith($localeProvider, $firewallMap, [new \DateTime(), 1, 5.0]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringInstantiation()
        ;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NonChannelLocaleListener::class);
    }

    function it_does_nothing_if_its_not_a_master_request(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        GetResponseEvent $event
    ) {
        $event->isMasterRequest()->willReturn(false);

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
        GetResponseEvent $event
    ) {
        $event->isMasterRequest()->willReturn(true);
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
        GetResponseEvent $event
    ) {
        $event->isMasterRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('lalaland', 'mock')
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
        GetResponseEvent $event
    ) {
        $event->isMasterRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('shop', 'mock')
        );

        $request->getLocale()->willReturn('en');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en', 'ga_IE']);

        $this
            ->shouldNotThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }

    function it_throws_not_found_exception_if_request_locale_is_not_present_in_provider(
        LocaleProviderInterface $localeProvider,
        FirewallMap $firewallMap,
        Request $request,
        GetResponseEvent $event
    ) {
        $event->isMasterRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $firewallMap->getFirewallConfig($request)->willReturn(
            new FirewallConfig('shop', 'mock')
        );

        $request->getLocale()->willReturn('en');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['ga', 'ga_IE']);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('restrictRequestLocale', [$event])
        ;
    }
}
