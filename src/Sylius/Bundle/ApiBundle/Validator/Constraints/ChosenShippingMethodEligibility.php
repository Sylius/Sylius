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

/** @experimental */
final class ChosenShippingMethodEligibility extends Constraint
{
    /** @var string */
    public $message = 'sylius.shipping_method.not_available';

    /** @var string */
    public $notFoundMessage = 'sylius.shipping_method.not_found';

    /** @var string */
    public $shippingAddressNotFoundMessage = 'sylius.shipping_method.shipping_address_not_found';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_chosen_shipping_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
