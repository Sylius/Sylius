<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\InventoryBundle\Operator\InsufficientStockException;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InsufficientStockExceptionListener
{
    protected $router;
    protected $session;
    protected $translator;
    protected $redirectTo;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof InsufficientStockException) {
            return;
        }

        $this->session->getFlashBag()->add(
            'error',
            $this->translator->trans(
                'sylius.checkout.out_of_stock',
                array(
                    '%quantity%' => $exception->getStockable()->getOnHand(),
                    '%name%'     => $exception->getStockable()->getInventoryName(),
                ),
                'flashes'
            )
        );

        $event->setResponse(new RedirectResponse(
            $event->getRequest()->headers->get('referer')
        ));
    }
}
