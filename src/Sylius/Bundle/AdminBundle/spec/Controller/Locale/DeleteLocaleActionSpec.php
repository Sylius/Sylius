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

namespace spec\Sylius\Bundle\AdminBundle\Controller\Locale;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Remover\LocaleRemoverInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DeleteLocaleActionSpec extends ObjectBehavior
{
    function let(LocaleRemoverInterface $localeRemover): void
    {
        $this->beConstructedWith($localeRemover);
    }

    function it_removes_a_locale(
        LocaleRemoverInterface $localeRemover,
        Request $request,
        ParameterBag $attributes,
        HeaderBag $headerBag,
        Session $session,
        FlashBag $flashBag,
    ): void {
        $request->attributes = $attributes;
        $request->headers = $headerBag;
        $request->getSession()->willReturn($session);

        $attributes->get('id')->willReturn(1);

        $localeRemover->removeById(1)->shouldBeCalled();

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('success', 'sylius.locale.delete.success')->shouldBeCalled();

        $headerBag->get('referer')->willReturn('/referer');

        $this($request)->shouldBeLike(new RedirectResponse('/referer'));
    }

    function it_throws_not_found_exception_when_a_locale_is_not_found(
        LocaleRemoverInterface $localeRemover,
        Request $request,
        ParameterBag $attributes,
        HeaderBag $headerBag,
        Session $session,
        FlashBag $flashBag,
    ): void {
        $request->attributes = $attributes;
        $request->headers = $headerBag;
        $request->getSession()->willReturn($session);

        $attributes->get('id')->willReturn("1");

        $localeRemover->removeById(1)->willThrow(LocaleNotFoundException::class);

        $session->getFlashBag()->willReturn($flashBag);

        $headerBag->get('referer')->willReturn('/referer');

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$request]);
    }

    function it_throws_locale_is_used_exception_when_a_locale_is_used(
        LocaleRemoverInterface $localeRemover,
        Request $request,
        ParameterBag $attributes,
        HeaderBag $headerBag,
        Session $session,
        FlashBag $flashBag,
    ): void {
        $request->attributes = $attributes;
        $request->headers = $headerBag;
        $request->getSession()->willReturn($session);

        $attributes->get('id')->willReturn(1);

        $localeRemover->removeById(1)->willThrow(LocaleIsUsedException::class);

        $session->getFlashBag()->willReturn($flashBag);

        $flashBag->add('error', 'sylius.locale.delete.is_used')->shouldBeCalled();

        $headerBag->get('referer')->willReturn('/referer');

        $this($request)->shouldBeLike(new RedirectResponse('/referer'));
    }
}
