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
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
class CheckoutCompletionValidator extends ConstraintValidator
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private FactoryInterface $stateMachineFactory,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var CheckoutCompletion $constraint */
        Assert::isInstanceOf($constraint, CheckoutCompletion::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if ($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)) {
            return;
        }

        $this->context->addViolation($constraint->message, [
            '%currentState%' => $stateMachine->getState(),
            '%possibleTransitions%' => implode(', ', $stateMachine->getPossibleTransitions()),
        ]);
    }
}
