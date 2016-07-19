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

use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PurchaseListener
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
     * @param PurchaseCompleteEvent $event
     */
    public function abandonCart(PurchaseCompleteEvent $event)
    {
        if (in_array($event->getSubject()->getState(), [PaymentInterface::STATE_PENDING, PaymentInterface::STATE_PROCESSING, PaymentInterface::STATE_COMPLETED])) {
            return;
        }

        $event->setResponse(new RedirectResponse(
            $this->router->generate($this->redirectTo)
        ));
    }

    /**
     * @param PurchaseCompleteEvent $event
     */
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

            case PaymentInterface::STATE_NEW:
                $type = 'notice';
                $message = 'sylius.checkout.new';
                break;

            case PaymentInterface::STATE_VOID:
            case PaymentInterface::STATE_CANCELLED:
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
            $this->translator->trans($message, [], 'flashes')
        );
    }
}
