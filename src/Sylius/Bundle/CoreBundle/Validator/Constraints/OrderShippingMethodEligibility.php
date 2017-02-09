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

use Symfony\Component\Validator\Constraint;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderShippingMethodEligibility extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.order.shipping_method_eligibility';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_order_shipping_method_eligibility_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
