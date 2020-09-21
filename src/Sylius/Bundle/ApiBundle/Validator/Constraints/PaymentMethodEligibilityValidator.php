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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class PaymentMethodEligibilityValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function validate($command, Constraint $constraint): void
    {
        Assert::isInstanceOf($command, OrderTokenValueAwareInterface::class);

        /** @var PaymentMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, PaymentMethodEligibility::class);

        /** @var OrderInterface $order */
        Assert::notNull($order = $this->orderRepository->findOneBy(['tokenValue' => $command->getOrderTokenValue()]));

        /** @var PaymentInterface $orderPayment */
        Assert::notNull($orderPayments = $order->getPayments());

        foreach($orderPayments as $orderPayment) {
            if (!$orderPayment->getMethod()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%paymentMethodName%' => $orderPayment->getMethod()->getName()]
                );
            }
        }
    }
}
