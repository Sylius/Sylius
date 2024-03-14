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

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Bundle\CoreBundle\MessageDispatcher\ResendShipmentConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResendShipmentConfirmationEmailAction
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private ResendShipmentConfirmationEmailDispatcherInterface|ShipmentEmailManagerInterface $shipmentEmailManager,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack|SessionInterface $requestStackOrSession,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }

        if ($this->shipmentEmailManager instanceof ShipmentEmailManagerInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                ShipmentEmailManagerInterface::class,
                self::class,
                ResendShipmentConfirmationEmailDispatcherInterface::class,
            );

            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'The argument name $shipmentEmailManager in the constructor of %s is deprecated and will be renamed to $resendShipmentConfirmationDispatcher in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function __invoke(Request $request): Response
    {
        $shipmentId = $request->attributes->get('id', '');

        if (!$this->csrfTokenManager->isTokenValid(
            new CsrfToken($shipmentId, (string) $request->query->get('_csrf_token', '')),
        )) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($shipmentId);
        if ($shipment === null) {
            throw new NotFoundHttpException(sprintf('The shipment with id %s has not been found', $shipmentId));
        }

        $this->sendConfirmationEmailOrDispatchResendShipmentConfirmation($shipment);

        FlashBagProvider
            ::getFlashBag($this->requestStackOrSession)
            ->add('success', 'sylius.email.shipment_confirmation_resent')
        ;

        return new RedirectResponse($request->headers->get('referer'));
    }

    private function sendConfirmationEmailOrDispatchResendShipmentConfirmation(ShipmentInterface $shipment): void
    {
        if ($this->shipmentEmailManager instanceof ShipmentEmailManagerInterface) {
            $this->shipmentEmailManager->sendConfirmationEmail($shipment);
        } else {
            $this->shipmentEmailManager->dispatch($shipment);
        }
    }
}
