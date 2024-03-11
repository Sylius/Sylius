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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\TransitionInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CheckoutCompletionValidator extends ConstraintValidator
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private FactoryInterface|StateMachineInterface $stateMachineFactory,
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

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var CheckoutCompletion $constraint */
        Assert::isInstanceOf($constraint, CheckoutCompletion::class);

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        Assert::isInstanceOf($order, OrderInterface::class);

        $stateMachine = $this->getStateMachine();

        if ($stateMachine->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            return;
        }

        $this->context->addViolation($constraint->message, [
            '%currentState%' => $order->getCheckoutState(),
            '%possibleTransitions%' => implode(
                ', ',
                array_map(
                    fn (TransitionInterface $transition) => $transition->getName(),
                    $stateMachine->getEnabledTransitions($order, OrderCheckoutTransitions::GRAPH),
                ),
            ),
        ]);
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
