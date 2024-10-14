<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Sylius\Bundle\PaymentBundle\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/** @experimental */
final class PaymentRequestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
        ];
    }

    public function postWrite(ViewEvent $event): void
    {
        $paymentRequest = $event->getControllerResult();
        if (!$paymentRequest instanceof PaymentRequestInterface) {
            return;
        }

        if (in_array($event->getRequest()->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH], true)) {
            $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);
        }
    }
}
