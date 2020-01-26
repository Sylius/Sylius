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

use Sylius\Bundle\AdminBundle\Shipper\OrderShipmentShipperInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\Assert\Assert;

final class ShipShipmentAction
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var OrderShipmentShipperInterface */
    private $orderShipmentShipper;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        OrderShipmentShipperInterface $orderShipmentShipper
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->orderShipmentShipper = $orderShipmentShipper;
    }

    public function __invoke(Request $request, string $id): Response
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($id, $request->request->get('_csrf_token')))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $shipment = $this->shipmentRepository->find($id);
        if ($shipment === null) {
            throw new NotFoundHttpException();
        }

        $trackingCode = $request->request->get('sylius_shipment_ship_tracking');

        $this->orderShipmentShipper->ship($shipment, $trackingCode);

        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', 'sylius.shipment.shipped');

        return new RedirectResponse($request->headers->get('referer'));
    }
}
