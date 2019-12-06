<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class CheckoutController
{
    /** @var CartContextInterface */
    private $cartContext;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(CartContextInterface $cartContext, OrderRepositoryInterface $orderRepository, OrderProcessorInterface $orderProcessor, ShippingMethodRepositoryInterface $shippingMethodRepository, EngineInterface $templatingEngine)
    {
        $this->cartContext = $cartContext;
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->templatingEngine = $templatingEngine;
    }

    public function shippingFeeAction(Request $request, string $shippingMethodCode): Response
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $shippingMethodCode]);
        if (!$shippingMethod) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                sprintf('Shipping method ID: "%d" is invalid.', $shippingMethodId)
            );
        }

        $cart = $this->cartContext->getCart();
        if (count($cart->getShipments()) == 0) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                sprintf('Shipment is missing for cart ID: "%s"', $cart->getId())
            );
        }

        $shipment = $cart->getShipments()[0];
        $shipment->setMethod($shippingMethod);
        $this->orderProcessor->process($cart);

        return new JsonResponse([
            'content' => $this->templatingEngine->render('@SyliusShop/Checkout/_summary.html.twig', [
                'order' => $cart,
            ]),
        ]);
    }
}
