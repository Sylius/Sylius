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
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
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

    /** @var PaymentMethodChangerInterface */
    private $paymentMethodChanger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        FactoryInterface $stateMachineFactory,
        PaymentMethodChangerInterface $paymentMethodChanger
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->paymentMethodChanger = $paymentMethodChanger;
    }

    public function __invoke(ChoosePaymentMethod $choosePaymentMethod): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $choosePaymentMethod->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        $paymentMethodCode = $choosePaymentMethod->paymentMethodCode;
        $paymentId = $choosePaymentMethod->paymentId;

        if ($cart->getState() === OrderInterface::STATE_NEW) {
            $this->paymentMethodChanger->changePaymentMethod($paymentMethodCode, $paymentId, $cart);

            return $cart;
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ]);
        Assert::notNull($paymentMethod, 'Payment method has not been found');

        $payment = $this->paymentRepository->findOneByOrderId($paymentId, $cart->getId());
        Assert::notNull($payment, 'Can not find payment with given identifier.');

        if ($cart->getState() === OrderInterface::STATE_CART) {
            $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

            Assert::true(
                $stateMachine->can(
                OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT
            ),
                'Order cannot have payment method assigned.'
            );

            $payment->setMethod($paymentMethod);
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

            return $cart;
        }

        throw new \InvalidArgumentException('Payment method can not be set');
    }
}
