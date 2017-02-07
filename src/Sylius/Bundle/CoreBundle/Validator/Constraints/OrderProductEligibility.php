<?php

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class OrderProductEligibility extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.order.product_eligibility';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_order_product_eligibility_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
