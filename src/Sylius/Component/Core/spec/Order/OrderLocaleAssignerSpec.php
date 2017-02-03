<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OrderLocaleAssignerSpec extends ObjectBehavior
{
    function it_assigns_locale_to_an_order(LocaleContextInterface $localeContext, OrderInterface $order)
    {
        $this->beConstructedWith($localeContext);

        $localeContext->getLocaleCode()->willReturn('pl_PL');

        $order->setLocaleCode('pl_PL')->shouldBeCalled();

        $this->assignLocale($order);
    }
}
