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

use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PurchaseListener
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->session = $session;
        $this->translator = $translator;
    }

    public function addFlash(GenericEvent $event)
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
            $this->translator->trans($message, array(), 'flashes')
        );
    }
}
