<?php

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
        $this->beConstructedWith($router, $session, $translator, 'url');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\InsufficientStockExceptionListener');
    }

    function it_does_not_perform_any_action_if_the_evet_exception_is_not_valid(
        GetResponseForExceptionEvent $event,
        SessionInterface $session
    ) {
        $exceptionClass = new \Exception();

        $session->getBag(Argument::any())->shouldNotBeCalled();

        $event->getException()->shouldBeCalled()->willReturn($exceptionClass);

        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $this->onKernelException($event);
    }

    function it_perform_the_action_successfully(
        GetResponseForExceptionEvent $event,
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        InsufficientStockException $exception,
        StockableInterface $stockable,
        FlashBagInterface $flashBag
    ) {
        $event->getException()->shouldBeCalled()->willReturn($exception);
        $event->setResponse(Argument::type('Symfony\Component\HttpFoundation\RedirectResponse'))->shouldBeCalled();

        $router->generate('url')->shouldBeCalled()->willReturn('path_url');

        $exception->getStockable()->shouldBeCalled()->willReturn($stockable);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(20);
        $stockable->getInventoryName()->shouldBeCalled()->willReturn('Inventory Name');

        $translator->trans(
            'sylius.checkout.out_of_stock',
            array(
                '%quantity%' => 20,
                '%name%' => 'Inventory Name',
            ),
            'flashes'
        )
        ->shouldBeCalled()
        ->willReturn('Out Of Stock Message');

        $session->getBag('flashes')->shouldBeCalled()->willReturn($flashBag);
        $flashBag->add('notice', 'Out Of Stock Message')->shouldBeCalled();

        $this->onKernelException($event);
    }
}
