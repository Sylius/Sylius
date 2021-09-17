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

use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddressOrderHandler implements MessageHandlerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private ObjectManager $manager;

    private StateMachineFactoryInterface $stateMachineFactory;

    private AddressMapperInterface $addressMapper;

    private CustomerProviderInterface $customerProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressMapperInterface $addressMapper,
        CustomerProviderInterface $customerProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->manager = $manager;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->addressMapper = $addressMapper;
        $this->customerProvider = $customerProvider;
    }

    public function __invoke(AddressOrder $addressOrder): OrderInterface
    {
        $tokenValue = $addressOrder->orderTokenValue;

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $tokenValue));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS),
            sprintf('Order with %s token cannot be addressed.', $tokenValue)
        );

        if (null === $order->getCustomer()) {
            Assert::notNull($addressOrder->email, sprintf('Visitor should provide an email.'));

            $order->setCustomer($this->customerProvider->provide($addressOrder->email));
        }

        /** @var AddressInterface|null $billingAddress */
        $billingAddress = $order->getBillingAddress();
        /** @var AddressInterface|null $shippingAddress */
        $shippingAddress = $order->getShippingAddress();

        if ($billingAddress !== null) {
            $order->setBillingAddress($this->addressMapper->mapExisting($billingAddress, $addressOrder->billingAddress));
        } else {
            $order->setBillingAddress($addressOrder->billingAddress);
        }

        $newShippingAddress = $addressOrder->shippingAddress ?? clone $addressOrder->billingAddress;

        if ($shippingAddress !== null) {
            $order->setShippingAddress($this->addressMapper->mapExisting($shippingAddress, $newShippingAddress));
        } else {
            $order->setShippingAddress($newShippingAddress);
        }

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->manager->persist($order);

        return $order;
    }
}
