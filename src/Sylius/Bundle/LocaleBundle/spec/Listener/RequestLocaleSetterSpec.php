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

namespace spec\Sylius\Bundle\LocaleBundle\Listener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestLocaleSetterSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeContext, $localeProvider);
    }

    function it_sets_locale_and_default_locale_on_request(
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request
    ): void {
        $event->getRequest()->willReturn($request);

        $localeContext->getLocaleCode()->willReturn('pl_PL');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $request->setLocale('pl_PL')->shouldBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
