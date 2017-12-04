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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class OrderShippingMethodEligibility extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.order.shipping_method_eligibility';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_order_shipping_method_eligibility_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
