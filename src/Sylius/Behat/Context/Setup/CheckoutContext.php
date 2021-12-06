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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class CheckoutContext implements Context
{
    private OrderRepositoryInterface $orderRepository;

    private RepositoryInterface $shippingMethodRepository;

    private RepositoryInterface $paymentMethodRepository;

    private MessageBusInterface $commandBus;

    private FactoryInterface $addressFactory;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $paymentMethodRepository,
        MessageBusInterface $commandBus,
        FactoryInterface $addressFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->orderRepository = $orderRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->commandBus = $commandBus;
        $this->addressFactory = $addressFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I have proceeded through checkout process in the :localeCode locale with email :email
     */
    public function iHaveProceededThroughCheckoutProcessInTheLocaleWithEmail(string $localeCode, string $email)
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($cartToken);
        Assert::notNull($cart);

        $cart->setLocaleCode($localeCode);

        $command = new UpdateCart($email, $this->getDefaultAddress());
        $command->setOrderTokenValue($cartToken);
        $this->commandBus->dispatch($command);

        $this->completeCheckout($cart);
    }

    /**
     * @Given I have proceeded through checkout process
     */
    public function iHaveProceededThroughCheckoutProcess(): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($cartToken);
        Assert::notNull($cart);

        $command = new UpdateCart(null, $this->getDefaultAddress());
        $command->setOrderTokenValue($cartToken);
        $this->commandBus->dispatch($command);

        $this->completeCheckout($cart);
    }

    private function getDefaultAddress(): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();

        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $address->setCountryCode('US');
        $address->setFirstName('Richy');
        $address->setLastName('Rich');

        return $address;
    }

    private function completeCheckout(OrderInterface $order): void
    {
        $command = new ChooseShippingMethod($this->shippingMethodRepository->findOneBy([])->getCode());
        $command->setOrderTokenValue($order->getTokenValue());

        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();

        $command->setSubresourceId((string) $shipment->getId());
        $this->commandBus->dispatch($command);

        $command = new ChoosePaymentMethod($this->paymentMethodRepository->findOneBy([])->getCode());
        $command->setOrderTokenValue($order->getTokenValue());

        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();
        $command->setSubresourceId((string) $payment->getId());

        $this->commandBus->dispatch($command);
    }
}
