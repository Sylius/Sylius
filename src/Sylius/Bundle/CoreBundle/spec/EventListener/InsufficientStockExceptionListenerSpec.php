<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\InsufficientStockException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class InsufficientStockExceptionListenerSpec extends ObjectBehavior
{
    function let(
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->beConstructedWith($router, $session, $translator, 'redirect_to_url');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\InsufficientStockExceptionListener');
    }

    function its_event_exception_does_not_have_an_instance_of_insufficient_stock_exception_action_must_finish(
        GetResponseForExceptionEvent $event
    ) {
        $event->getException()->shouldBeCalled()->willReturn(new \RuntimeException());

        $this->onKernelException($event)->shouldReturn(null);
    }

    function it_performs_kernel_exception_action_successfully(
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        GetResponseForExceptionEvent $event,
        InsufficientStockException $exception,
        FlashBagInterface $flashBag,
        StockableInterface $stockable
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn('30');
        $stockable->getInventoryName()->shouldBeCalled()->willReturn('Inventory Name');

        $exception->getStockable()->shouldBeCalledTimes(2)->willReturn($stockable);

        $event->getException()->shouldBeCalled()->willReturn($exception);
        $event->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $translator->trans(
            'sylius.checkout.out_of_stock',
            [
                '%quantity%' => '30',
                '%name%' => 'Inventory Name',
            ],
            'flashes'
        )->shouldBeCalled()->willReturn('message translated');

        $flashBag->add('notice', 'message translated')->shouldBeCalled();

        $session->getBag('flashes')->shouldBeCalled()->willReturn($flashBag);

        $router->generate('redirect_to_url')->shouldBeCalled()->willReturn('url');

        $this->onKernelException($event)->shouldReturn(null);
    }
}
