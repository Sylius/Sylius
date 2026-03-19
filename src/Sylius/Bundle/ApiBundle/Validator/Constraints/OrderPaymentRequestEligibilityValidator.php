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

use Sylius\Bundle\ApiBundle\Command\Payment\AddPaymentRequest;
use Sylius\Bundle\ApiBundle\Command\Payment\UpdatePaymentRequest;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderPaymentRequestEligibilityValidator extends ConstraintValidator
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, OrderPaymentRequestEligibility::class);

        if ($value instanceof AddPaymentRequest) {
            $this->validateAddPaymentRequest($value, $constraint);

            return;
        }

        if ($value instanceof UpdatePaymentRequest) {
            $this->validateUpdatePaymentRequest($value, $constraint);

            return;
        }

        Assert::true(false, sprintf('Expected an instance of %s or %s. Got: %s', AddPaymentRequest::class, UpdatePaymentRequest::class, get_debug_type($value)));
    }

    private function validateAddPaymentRequest(AddPaymentRequest $value, OrderPaymentRequestEligibility $constraint): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->orderTokenValue]);
        if (null === $order) {
            return;
        }

        if ($order->getCheckoutState() !== OrderCheckoutStates::STATE_COMPLETED) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function validateUpdatePaymentRequest(UpdatePaymentRequest $value, OrderPaymentRequestEligibility $constraint): void
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($value->hash);
        if (null === $paymentRequest) {
            return;
        }

        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();

        /** @var OrderInterface|null $order */
        $order = $payment->getOrder();
        if (null === $order) {
            return;
        }

        if ($order->getCheckoutState() !== OrderCheckoutStates::STATE_COMPLETED) {
            $this->context->addViolation($constraint->message);
        }
    }
}
