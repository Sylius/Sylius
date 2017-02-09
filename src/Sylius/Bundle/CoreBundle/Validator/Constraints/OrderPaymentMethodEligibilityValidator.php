<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderPaymentMethodEligibilityValidator extends ConstraintValidator
{
    /**
     * @param OrderInterface $order
     *
     * {@inheritdoc}
     */
    public function validate($order, Constraint $constraint)
    {
        Assert::isInstanceOf($order, OrderInterface::class);

        $payments = $order->getPayments();

        foreach ($payments as $payment) {
            if (!$payment->getMethod()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%paymentMethodName%' => $payment->getMethod()->getName()]
                );
            }
        }
    }
}
