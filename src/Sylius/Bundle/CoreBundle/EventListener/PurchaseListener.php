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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

class PurchaseListener
{
    protected $cartProvider;
    protected $router;
    protected $session;
    protected $translator;

    public function __construct(CartProviderInterface $cartProvider, UrlGeneratorInterface $router, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->cartProvider = $cartProvider;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function abandonCart(PurchaseCompleteEvent $event)
    {
        if (in_array($event->getSubject()->getState(), array(PaymentInterface::STATE_PENDING, PaymentInterface::STATE_PROCESSING, PaymentInterface::STATE_COMPLETED))) {
            $this->cartProvider->abandonCart();

            return;
        }

        $event->setResponse(new RedirectResponse(
            $this->router->generate('sylius_checkout_payment')
        ));
    }

    public function addFlash(PurchaseCompleteEvent $event)
    {
        switch ($event->getSubject()->getState()) {
            case PaymentInterface::STATE_COMPLETED:
                $type = 'success';
                $message = 'sylius.checkout.success';
                break;

            case PaymentInterface::STATE_PROCESSING:
            case PaymentInterface::STATE_PENDING:
                $type = 'notice';
                $message = 'sylius.checkout.processing';
                break;

            case PaymentInterface::STATE_VOID:
                $type = 'notice';
                $message = 'sylius.checkout.canceled';
                break;

            case PaymentInterface::STATE_FAILED:
                $type = 'error';
                $message = 'sylius.checkout.failed';
                break;

            default:
                $type = 'error';
                $message = 'sylius.checkout.unknown';
                break;
        }

        $this->session->getBag('flashes')->add(
            $type,
            $this->translator->trans($message, array(), 'flashes')
        );
    }
}
