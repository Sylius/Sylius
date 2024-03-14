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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeChangedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ChoosePaymentMethodHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private FactoryInterface|StateMachineInterface $stateMachineFactory,
        private PaymentMethodChangerInterface $paymentMethodChanger,
    ) {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the fourth argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
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
            $stateMachine = $this->getStateMachine();
            Assert::true(
                $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT),
                'Order cannot have payment method assigned.',
            );

            $payment->setMethod($paymentMethod);
            $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

            return $cart;
        }

        throw new PaymentMethodCannotBeChangedException();
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
