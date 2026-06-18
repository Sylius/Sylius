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

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ChosenPaymentMethodEligibility extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $notAvailable = null,
        ?string $notExist = null,
        ?string $paymentNotFound = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $notAvailable ??= $options['notAvailable'] ?? null;
            $notExist ??= $options['notExist'] ?? null;
            $paymentNotFound ??= $options['paymentNotFound'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->notAvailable = $notAvailable ?? $this->notAvailable;
        $this->notExist = $notExist ?? $this->notExist;
        $this->paymentNotFound = $paymentNotFound ?? $this->paymentNotFound;
    }

    public string $notAvailable = 'sylius.payment_method.not_available';

    public string $notExist = 'sylius.payment_method.not_exist';

    public string $paymentNotFound = 'sylius.payment.not_found';

    public function validatedBy(): string
    {
        return 'sylius_api_chosen_payment_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
