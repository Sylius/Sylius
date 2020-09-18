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

    public function validate($value, Constraint $constraint): void
    {
        Assert::notNull($value);

        /** @var OrderInterface $order */
        Assert::notNull($order = $this->orderRepository->findOneBy(['tokenValue' => $value]));

        /** @var PaymentInterface $orderPayment */
        Assert::notNull($orderPayment = $order->getPayments()[0]);

        if (!$orderPayment->getMethod()->isEnabled()) {
            $this->context->addViolation(
                $constraint->message,
                ['%paymentMethodName%' => $orderPayment->getMethod()->getName()]
            );
        }
    }
}
