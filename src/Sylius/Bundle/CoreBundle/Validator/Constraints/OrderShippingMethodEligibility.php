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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class OrderShippingMethodEligibility extends Constraint
{
    /**
     * @param array<array-key, mixed> $options
     */
    public function __construct(
        array $options = [],
        public string $message = 'sylius.order.shipping_method_eligibility',
        public string $methodNotAvailableMessage = 'sylius.order.shipping_method_not_available',
        mixed $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMethodNotAvailableMessage(): string
    {
        return $this->methodNotAvailableMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_order_shipping_method_eligibility_validator';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
