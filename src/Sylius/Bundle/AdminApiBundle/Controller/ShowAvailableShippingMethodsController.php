<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShowAvailableShippingMethodsController
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @var ViewHandlerInterface
     */
    private $restViewHandler;

    /**
     * @var ServiceRegistryInterface
     */
    private $calculators;

    /**
     * @param FactoryInterface $stateMachineFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param ViewHandlerInterface $restViewHandler
     * @param ServiceRegistryInterface $calculators
     */
    public function __construct(
        FactoryInterface $stateMachineFactory,
        OrderRepositoryInterface $orderRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ViewHandlerInterface $restViewHandler,
        ServiceRegistryInterface $calculators
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderRepository = $orderRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->restViewHandler = $restViewHandler;
        $this->calculators = $calculators;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        /** @var OrderInterface $cart */
        $cart = $this->getCartOr404($request->attributes->get('orderId'));

        if (!$this->isCheckoutTransitionPossible($cart, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING)) {
            throw new BadRequestHttpException('The shipment methods cannot be resolved in the current state of cart!');
        }

        $shipments = [];

        foreach ($cart->getShipments() as $shipment) {
            $shipments['shipments'][] = [
                'methods' => $this->getCalculatedShippingMethods($shipment, $cart->getLocaleCode()),
            ];
        }

        return $this->restViewHandler->handle(View::create($shipments));
    }

    /**
     * @param mixed $cartId
     *
     * @return OrderInterface
     */
    private function getCartOr404($cartId)
    {
        $cart = $this->orderRepository->findCartById($cartId);

        if (null === $cart) {
            throw new NotFoundHttpException(sprintf("The cart with %s id could not be found!", $cartId));
        }

        return $cart;
    }

    /**
     * @param OrderInterface $cart
     * @param string $transition
     *
     * @return bool
     */
    private function isCheckoutTransitionPossible(OrderInterface $cart, $transition)
    {
        return $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->can($transition);
    }

    /**
     * @param ShipmentInterface $shipment
     * @param string $locale
     *
     * @return array
     */
    private function getCalculatedShippingMethods(ShipmentInterface $shipment, $locale)
    {
        $shippingMethods =  $this->shippingMethodsResolver->getSupportedMethods($shipment);

        $rawShippingMethods = [];

        foreach ($shippingMethods as $shippingMethod) {
            $calculator = $this->calculators->get($shippingMethod->getCalculator());

            $rawShippingMethods[] = [
                'id' => $shippingMethod->getId(),
                'code' => $shippingMethod->getCode(),
                'name' => $shippingMethod->getTranslation($locale)->getName(),
                'description' => $shippingMethod->getTranslation($locale)->getDescription(),
                'price' => $calculator->calculate($shipment, $shippingMethod->getConfiguration()),
            ];
        }

        return $rawShippingMethods;
    }
}
