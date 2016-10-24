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
use Sylius\Bundle\CoreBundle\EventListener\InsufficientStockExceptionListener;
use Sylius\Component\Inventory\Exception\InsufficientStockException;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
final class InsufficientStockExceptionListenerSpec extends ObjectBehavior
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
        $this->shouldHaveType(InsufficientStockExceptionListener::class);
    }

    function its_event_exception_does_not_have_an_instance_of_insufficient_stock_exception_action_must_finish(
        GetResponseForExceptionEvent $event
    ) {
        $event->getException()->willReturn(new \RuntimeException());

        $this->onKernelException($event);
    }

    function it_performs_kernel_exception_action_successfully(
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        GetResponseForExceptionEvent $event,
        FlashBagInterface $flashBag,
        StockableInterface $stockable
    ) {
        $stockable->getOnHand()->willReturn('30');
        $stockable->getInventoryName()->willReturn('Inventory Name');

        $event->getException()->willReturn(new InsufficientStockException($stockable->getWrappedObject(), 42));
        $event->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $translator->trans(
            'sylius.checkout.out_of_stock',
            [
                '%quantity%' => '30',
                '%name%' => 'Inventory Name',
            ],
            'flashes'
        )->willReturn('message translated');

        $flashBag->add('notice', 'message translated')->shouldBeCalled();

        $session->getBag('flashes')->willReturn($flashBag);

        $router->generate('redirect_to_url')->willReturn('url');

        $this->onKernelException($event);
    }
}
