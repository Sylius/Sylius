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
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\Exception\OrderTotalHasChangedException;
use Sylius\Bundle\ApiBundle\Event\OrderCompleted;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private FactoryInterface|StateMachineInterface $stateMachineFactory,
        private MessageBusInterface $commandBus,
        private MessageBusInterface $eventBus,
        private OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ) {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the second argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    /**
     * @throws StateMachineExecutionException
     * @throws OrderTotalHasChangedException
     */
    public function __invoke(CompleteOrder $completeOrder): OrderInterface
    {
        $orderTokenValue = $completeOrder->orderTokenValue;

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $orderTokenValue]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $orderTokenValue));
        Assert::notNull($cart->getCustomer(), 'Please enter your email before completing the order.');

        if ($completeOrder->notes !== null) {
            $cart->setNotes($completeOrder->notes);
        }

        $oldTotal = $cart->getTotal();

        if ($promotion = $this->orderPromotionsIntegrityChecker->check($cart)) {
            $this->commandBus->dispatch(
                new InformAboutCartRecalculation($promotion->getName()),
                [new DispatchAfterCurrentBusStamp()],
            );

            return $cart;
        }

        if ($oldTotal !== $cart->getTotal()) {
            throw new OrderTotalHasChangedException();
        }

        $stateMachine = $this->getStateMachine();
        Assert::true(
            $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE),
            sprintf('Order with %s token cannot be completed.', $orderTokenValue),
        );

        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->eventBus->dispatch(new OrderCompleted($cart->getTokenValue()), [new DispatchAfterCurrentBusStamp()]);

        return $cart;
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
