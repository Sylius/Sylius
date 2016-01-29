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
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $redirectTo;

    /**
     * @param UrlGeneratorInterface $router
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     * @param string $redirectTo
     */
    public function __construct(
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        $redirectTo
    ) {
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
        $this->redirectTo = $redirectTo;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof InsufficientStockException) {
            return;
        }

        $this->session->getBag('flashes')->add(
            'notice',
            $this->translator->trans(
                'sylius.checkout.out_of_stock',
                [
                    '%quantity%' => $exception->getStockable()->getOnHand(),
                    '%name%' => $exception->getStockable()->getInventoryName(),
                ],
                'flashes'
            )
        );

        $event->setResponse(
            new RedirectResponse($this->router->generate($this->redirectTo))
        );
    }
}
