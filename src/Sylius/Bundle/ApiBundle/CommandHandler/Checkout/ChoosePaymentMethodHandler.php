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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChoosePaymentMethodHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(ChoosePaymentMethod $choosePaymentMethod): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $choosePaymentMethod->paymentMethodCode,
        ]);
        Assert::notNull($paymentMethod, 'Payment method has not been found');

        $payment = $this->paymentRepository->findOneByOrderId($choosePaymentMethod->paymentId, $cart->getId());
        Assert::notNull($payment, 'Can not find payment with given identifier.');

        if (in_array(
            $cart->getCheckoutState(),
            [OrderCheckoutStates::STATE_SHIPPING_SELECTED, OrderCheckoutStates::STATE_SHIPPING_SKIPPED, OrderCheckoutStates::STATE_PAYMENT_SELECTED],
            true)
        ) {
            $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

            Assert::true($stateMachine->can(
                OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT),
                'Order cannot have payment method assigned.'
            );

            $payment->setMethod($paymentMethod);
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

            return $cart;
        }

        Assert::same(
            $payment->getState(),
            PaymentInterface::STATE_NEW,
            'Can not change payment method for this payment'
        );
        $payment->setMethod($paymentMethod);

        return $cart;
    }
}
