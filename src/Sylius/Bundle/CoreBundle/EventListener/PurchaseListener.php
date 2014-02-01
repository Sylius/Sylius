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
use Sylius\Bundle\PayumBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

class PurchaseListener
{
    /**
     * @var CartProviderInterface
     */
    private $cartProvider;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CartProviderInterface $cartProvider
     * @param UrlGeneratorInterface $router
     * @param SessionInterface      $session
     * @param TranslatorInterface   $translator
     */
    public function __construct(CartProviderInterface $cartProvider, UrlGeneratorInterface $router, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->cartProvider = $cartProvider;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param PurchaseCompleteEvent $event
     */
    public function abandonCart(PurchaseCompleteEvent $event)
    {
        $payment = $event->getSubject();
        $state   = $payment->getState();

        $this->addFlash($state);

        if (in_array($state, array(PaymentInterface::STATE_PENDING, PaymentInterface::STATE_PROCESSING, PaymentInterface::STATE_COMPLETED))) {
            $this->cartProvider->abandonCart();

            return;
        }

        $event->setResponse(new RedirectResponse(
            $this->router->generate('sylius_checkout_payment')
        ));
    }

    /**
     * @param string $state
     */
    private function addFlash($state)
    {
        switch ($state) {
            case PaymentInterface::STATE_COMPLETED:
                $type    = 'success';
                $message = 'sylius.checkout.success';
                break;

            case PaymentInterface::STATE_PROCESSING:
            case PaymentInterface::STATE_PENDING:
                $type    = 'notice';
                $message = 'sylius.checkout.processing';
                break;

            case PaymentInterface::STATE_CANCELLED:
                $type = 'notice';
                $message = 'sylius.checkout.cancelled';
                break;

            case PaymentInterface::STATE_FAILED:
                $type    = 'error';
                $message = 'sylius.checkout.failed';
                break;

            default:
                $type    = 'error';
                $message = 'sylius.checkout.unknown';
                break;
        }

        $this->session->getBag('flashes')->add($type, $this->translator->trans($message, array(), 'flashes'));
    }
}
