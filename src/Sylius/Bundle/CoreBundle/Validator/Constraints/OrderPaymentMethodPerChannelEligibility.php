<?php

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class OrderPaymentMethodPerChannelEligibility extends Constraint
{
    public string $message = 'sylius.order.payment_method_eligibility';

    public function validatedBy(): string
    {
        // Must match the alias from services (validators.xml)
        return 'sylius_order_payment_method_per_channel_eligibility_validator';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
