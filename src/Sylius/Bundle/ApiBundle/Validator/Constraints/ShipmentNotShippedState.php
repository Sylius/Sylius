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

use Symfony\Component\Validator\Constraint;

final class ShipmentNotShippedState extends Constraint
{
    /** @var string */
    public $message = 'sylius.shipment.shipped';

    public function validatedBy(): string
    {
        return 'sylius_api_shipment_not_shipped_state';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
