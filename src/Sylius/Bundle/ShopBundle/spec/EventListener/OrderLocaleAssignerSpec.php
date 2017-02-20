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
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OrderLocaleAssignerSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext)
    {
        $this->beConstructedWith($localeContext);
    }

    function it_assigns_locale_to_an_order(
        LocaleContextInterface $localeContext,
        OrderInterface $order,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($order);
        $localeContext->getLocaleCode()->willReturn('pl_PL');

        $order->setLocaleCode('pl_PL')->shouldBeCalled();

        $this->assignLocale($event);
    }

    function it_throws_invalid_argument_exception_if_subject_it_not_order(ResourceControllerEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('assignLocale', [$event]);
    }
}
