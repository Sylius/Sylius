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

namespace Sylius\Tests\Api\Utils;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Webmozart\Assert\Assert;

trait OrderPlacerTrait
{
    private MessageBusInterface $commandBus;

    private OrderRepositoryInterface $orderRepository;

    private bool $isSetUpOrderPlacerCalled = false;

    final public function setUpOrderPlacer(): void
    {
        $this->commandBus = self::getContainer()->get('sylius.command_bus');
        $this->orderRepository = $this->get('sylius.repository.order');

        $this->isSetUpOrderPlacerCalled = true;
    }

    protected function fulfillOrder(
        string $tokenValue,
        string $productVariantCode = 'MUG_BLUE',
        int $quantity = 3,
        string $email = 'sylius@example.com',
        ?\DateTimeImmutable $checkoutCompletedAt = null,
    ): OrderInterface {
        $this->checkSetUpOrderPlacerCalled();

        $this->pickUpCart($tokenValue);
        $this->addItemToCart($productVariantCode, $quantity, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue, $email);
        $this->dispatchShippingMethodChooseCommand(
            $tokenValue,
            'UPS',
            (string) $cart->getShipments()->first()->getId(),
        );
        $this->dispatchPaymentMethodChooseCommand(
            $tokenValue,
            'CASH_ON_DELIVERY',
            (string) $cart->getLastPayment()->getId(),
        );
        $order = $this->dispatchCompleteOrderCommand($tokenValue);
        $this->payOrder($order);
        $this->setCheckoutCompletedAt($order, $checkoutCompletedAt);

        return $order;
    }

    protected function placeOrder(
        string $tokenValue,
        string $email = 'sylius@example.com',
        string $productVariantCode = 'MUG_BLUE',
        int $quantity = 3,
        ?\DateTimeImmutable $checkoutCompletedAt = null,
        ?string $couponCode = null,
    ): OrderInterface {
        $this->checkSetUpOrderPlacerCalled();

        $this->pickUpCart($tokenValue);
        $this->addItemToCart($productVariantCode, $quantity, $tokenValue);
        $cart = $this->updateCartWithAddressAndCouponCode($tokenValue, $email, $couponCode);
        $this->dispatchShippingMethodChooseCommand(
            $tokenValue,
            'UPS',
            (string) $cart->getShipments()->first()->getId(),
        );
        $this->dispatchPaymentMethodChooseCommand(
            $tokenValue,
            'CASH_ON_DELIVERY',
            (string) $cart->getLastPayment()->getId(),
        );

        $order = $this->dispatchCompleteOrderCommand($tokenValue);

        return $order;
    }

    private function dispatchShippingMethodChooseCommand(
        string $tokenValue,
        string $shippingMethodCode = 'UPS',
        ?string $subresourceId = null,
    ): OrderInterface {
        $chooseShippingMethodCommand = new ChooseShippingMethod($shippingMethodCode);
        $chooseShippingMethodCommand->setOrderTokenValue($tokenValue);
        $chooseShippingMethodCommand->setSubresourceId($subresourceId);

        $envelope = $this->commandBus->dispatch($chooseShippingMethodCommand);

        return $envelope->last(HandledStamp::class)->getResult();
    }

    private function dispatchPaymentMethodChooseCommand(
        string $tokenValue,
        string $paymentMethodCode = 'CASH_ON_DELIVERY',
        ?string $subresourceId = null,
    ): OrderInterface {
        $choosePaymentMethodCommand = new ChoosePaymentMethod($paymentMethodCode);
        $choosePaymentMethodCommand->setOrderTokenValue($tokenValue);
        $choosePaymentMethodCommand->setSubresourceId($subresourceId);

        $envelope = $this->commandBus->dispatch($choosePaymentMethodCommand);

        return $envelope->last(HandledStamp::class)->getResult();
    }

    protected function dispatchCompleteOrderCommand(
        string $tokenValue,
    ): OrderInterface {
        $completeOrderCommand = new CompleteOrder();
        $completeOrderCommand->setOrderTokenValue($tokenValue);
        $envelope = $this->commandBus->dispatch($completeOrderCommand);

        return $envelope->last(HandledStamp::class)->getResult();
    }

    protected function cancelOrder(string $tokenValue): void
    {
        $objectManager = $this->get('doctrine.orm.entity_manager');

        $order = $this->orderRepository->findOneByTokenValue($tokenValue);
        Assert::notNull($order);

        $stateMachineFactory = $this->get('sm.factory');

        $stateMachine = $stateMachineFactory->get($order, OrderTransitions::GRAPH);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL);

        $objectManager->flush();
    }

    protected function payOrder(OrderInterface $order): OrderInterface
    {
        $objectManager = $this->get('doctrine.orm.entity_manager');

        $stateMachineFactory = $this->get('sm.factory');

        $stateMachine = $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);

        $objectManager->flush();

        return $order;
    }

    private function setCheckoutCompletedAt(
        OrderInterface $order,
        ?\DateTimeImmutable $checkoutCompletedAt,
    ): OrderInterface {
        $objectManager = $this->get('doctrine.orm.entity_manager');

        $order->setCheckoutCompletedAt($checkoutCompletedAt);

        $objectManager->flush();

        return $order;
    }

    protected function pickUpCart(string $tokenValue = 'nAWw2jewpA', string $channelCode = 'WEB'): string
    {
        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode($channelCode);

        $this->commandBus->dispatch($pickupCartCommand);

        return $tokenValue;
    }

    protected function addItemToCart(string $productVariantCode, int $quantity, string $tokenValue): string
    {
        $addItemToCartCommand = new AddItemToCart($productVariantCode, $quantity);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($addItemToCartCommand);

        return $tokenValue;
    }

    protected function updateCartWithAddress(
        string $tokenValue,
        string $email = 'sylius@example.com',
    ): OrderInterface {
        return $this->updateCartWithAddressAndCouponCode($tokenValue, $email);
    }

    protected function updateCartWithAddressAndCouponCode(
        string $tokenValue,
        string $email = 'sylius@example.com',
        ?string $couponCode = null,
    ): OrderInterface {
        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart(email: $email, billingAddress: $address, couponCode: $couponCode);
        $updateCartCommand->setOrderTokenValue($tokenValue);

        $envelope = $this->commandBus->dispatch($updateCartCommand);

        return $envelope->last(HandledStamp::class)->getResult();
    }

    private function checkSetUpOrderPlacerCalled(): void
    {
        if (!$this->isSetUpOrderPlacerCalled) {
            throw new \LogicException('The setUpOrderPlacer() method must be called in setUp() method.');
        }
    }
}
