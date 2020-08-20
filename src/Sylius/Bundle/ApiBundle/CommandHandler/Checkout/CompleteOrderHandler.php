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
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CompleteOrderHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var OrderEmailManagerInterface */
    private $emailManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $stateMachineFactory,
        OrderEmailManagerInterface $emailManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->emailManager = $emailManager;
    }

    public function __invoke(CompleteOrder $completeOrder): OrderInterface
    {
        $orderTokenValue = $completeOrder->orderTokenValue;

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $orderTokenValue]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $orderTokenValue));

        if ($completeOrder->notes !== null) {
            $cart->setNotes($completeOrder->notes);
        }

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE),
            sprintf('Order with %s token cannot be completed.', $orderTokenValue)
        );

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->emailManager->sendConfirmationEmail($cart);

        return $cart;
    }
}
