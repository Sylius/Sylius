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
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Webmozart\Assert\Assert;

trait OrderPlacerTrait
{
    protected function placeOrder(string $tokenValue, string $email = 'sylius@example.com'): OrderInterface
    {
        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart($email, $address);
        $updateCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($updateCartCommand);

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->get('sylius.repository.order');
        /** @var OrderInterface|null $cart */
        $cart = $orderRepository->findCartByTokenValue($tokenValue);
        Assert::notNull($cart);

        $chooseShippingMethodCommand = new ChooseShippingMethod('UPS');
        $chooseShippingMethodCommand->setOrderTokenValue($tokenValue);
        $chooseShippingMethodCommand->setSubresourceId((string) $cart->getShipments()->first()->getId());
        $commandBus->dispatch($chooseShippingMethodCommand);

        $choosePaymentMethodCommand = new ChoosePaymentMethod('CASH_ON_DELIVERY');
        $choosePaymentMethodCommand->setOrderTokenValue($tokenValue);
        $choosePaymentMethodCommand->setSubresourceId((string) $cart->getLastPayment()->getId());
        $commandBus->dispatch($choosePaymentMethodCommand);

        $completeOrderCommand = new CompleteOrder();
        $completeOrderCommand->setOrderTokenValue($tokenValue);
        $envelope = $commandBus->dispatch($completeOrderCommand);

        return $envelope->last(HandledStamp::class)->getResult();
    }
}
