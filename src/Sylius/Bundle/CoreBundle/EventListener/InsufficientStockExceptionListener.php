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

use Sylius\Component\Inventory\Operator\InsufficientStockException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InsufficientStockExceptionListener
{
    protected $router;
    protected $session;
    protected $translator;
    protected $redirectTo;

    public function __construct(UrlGeneratorInterface $router, SessionInterface $session, TranslatorInterface $translator, $redirectTo)
    {
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
        $this->redirectTo = $redirectTo;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if (!$e instanceof InsufficientStockException) {
            return;
        }

        $this->session->getBag('flashes')->add(
            'notice',
            $this->translator->trans(
                'sylius.checkout.out_of_stock',
                array(
                    '%quantity%' => $e->getStockable()->getOnHand(),
                    '%name%'     => $e->getStockable()->getInventoryName(),
                ),
                'flashes'
            )
        );

        $event->setResponse(new RedirectResponse(
            $this->router->generate($this->redirectTo)
        ));
    }
}
