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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShowAvailablePaymentMethodsController
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
     * @var PaymentMethodsResolverInterface
     */
    private $paymentMethodResolver;

    /**
     * @var ViewHandlerInterface
     */
    private $restViewHandler;

    /**
     * @param FactoryInterface $stateMachineFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentMethodsResolverInterface $paymentMethodResolver
     * @param ViewHandlerInterface $restViewHandler
     */
    public function __construct(
        FactoryInterface $stateMachineFactory,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodsResolverInterface $paymentMethodResolver,
        ViewHandlerInterface $restViewHandler
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderRepository = $orderRepository;
        $this->paymentMethodResolver = $paymentMethodResolver;
        $this->restViewHandler = $restViewHandler;
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

        if (!$this->isCheckoutTransitionPossible($cart, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT)) {
            throw new BadRequestHttpException('The payment methods cannot be resolved in the current state of cart!');
        }

        $payments = [];

        foreach ($cart->getPayments() as $payment) {
            $payments['payments'][] = [
                'methods' => $this->getPaymentMethods($payment, $cart->getLocaleCode()),
            ];
        }

        return $this->restViewHandler->handle(View::create($payments));
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
     * @param PaymentInterface $payment
     * @param string $locale
     *
     * @return array
     */
    private function getPaymentMethods(PaymentInterface $payment, $locale)
    {
        $paymentMethods =  $this->paymentMethodResolver->getSupportedMethods($payment);

        $rawPaymentMethods = [];

        foreach ($paymentMethods as $paymentMethod) {
            $rawPaymentMethods[] = [
                'id' => $paymentMethod->getId(),
                'code' => $paymentMethod->getCode(),
                'name' => $paymentMethod->getTranslation($locale)->getName(),
                'description' => $paymentMethod->getTranslation($locale)->getDescription(),
            ];
        }

        return $rawPaymentMethods;
    }
}
