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

use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendShipmentConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final readonly class ResendShipmentConfirmationEmailAction
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private ResendShipmentConfirmationEmailDispatcherInterface $resendShipmentConfirmationDispatcher,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack $requestStack,
    ) {
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

        $this->resendShipmentConfirmationDispatcher->dispatch($shipment);

        FlashBagProvider
            ::getFlashBag($this->requestStack)
            ->add('success', 'sylius.email.shipment_confirmation_resent')
        ;

        return new RedirectResponse($request->headers->get('referer'));
    }
}
