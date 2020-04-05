<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResendShipmentConfirmationEmailAction
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var ShipmentEmailManagerInterface */
    private $shipmentEmailManager;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var Session */
    private $session;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentEmailManagerInterface $shipmentEmailManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        SessionInterface $session
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentEmailManager = $shipmentEmailManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->session = $session;
    }

    public function __invoke(Request $request): Response
    {
        $shipmentId = $request->attributes->get('id');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($shipmentId, $request->query->get('_csrf_token')))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($shipmentId);
        if ($shipment === null) {
            throw new NotFoundHttpException(sprintf('The shipment with id %s has not been found', $shipmentId));
        }

        $this->shipmentEmailManager->sendConfirmationEmail($shipment);

        $this->session->getFlashBag()->add(
            'success',
            'sylius.email.shipment_confirmation_resent'
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
