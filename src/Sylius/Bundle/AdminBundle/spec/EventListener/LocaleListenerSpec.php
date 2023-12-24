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

namespace spec\Sylius\Bundle\AdminBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\HttpFoundation\Response;

final class LocaleListenerSpec extends ObjectBehavior
{
    function let(LocaleUsageCheckerInterface $localeUsageChecker): void
    {
        $this->beConstructedWith($localeUsageChecker);
    }

    function it_does_nothing_if_locale_is_not_used(
        LocaleUsageCheckerInterface $localeUsageChecker,
        LocaleInterface $locale,
        ResourceControllerEvent $event,
    ): void {
        $localeUsageChecker->isUsed('en_US')->willReturn(false);

        $locale->getCode()->willReturn('en_US');

        $event->getSubject()->willReturn($locale);
        $event->stop()->shouldNotBeCalled();

        $this->preDelete($event);
    }

    function it_stops_event_if_locale_is_used(
        LocaleUsageCheckerInterface $localeUsageChecker,
        LocaleInterface $locale,
        ResourceControllerEvent $event,
    ): void {
        $localeUsageChecker->isUsed('en_US')->willReturn(true);

        $locale->getCode()->willReturn('en_US');

        $event->getSubject()->willReturn($locale);
        $event->stop('sylius.locale.delete.is_used', 'error', [], Response::HTTP_UNPROCESSABLE_ENTITY)->shouldBeCalled();

        $this->preDelete($event);
    }
}
