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
        public string $notAvailableMessage = 'sylius.payment_method.not_available',
        public string $notExistMessage = 'sylius.payment_method.not_exist',
        public string $paymentNotFoundMessage = 'sylius.payment.not_found',
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

            $this->notAvailableMessage = $options['notAvailableMessage'] ?? $this->notAvailableMessage;
            $this->notExistMessage = $options['notExistMessage'] ?? $this->notExistMessage;
            $this->paymentNotFoundMessage = $options['paymentNotFoundMessage'] ?? $this->paymentNotFoundMessage;
            $notAvailable ??= $options['notAvailable'] ?? null;
            $notExist ??= $options['notExist'] ?? null;
            $paymentNotFound ??= $options['paymentNotFound'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $notAvailable) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "notAvailable" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "notAvailableMessage" instead.',
                static::class,
            );

            $this->notAvailableMessage = $notAvailable;
        }

        if (null !== $notExist) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "notExist" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "notExistMessage" instead.',
                static::class,
            );

            $this->notExistMessage = $notExist;
        }

        if (null !== $paymentNotFound) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "paymentNotFound" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "paymentNotFoundMessage" instead.',
                static::class,
            );

            $this->paymentNotFoundMessage = $paymentNotFound;
        }

        parent::__construct(groups: $groups, payload: $payload);
        $this->notAvailable = $this->notAvailableMessage;
        $this->notExist = $this->notExistMessage;
        $this->paymentNotFound = $this->paymentNotFoundMessage;
    }

    /** @deprecated since Sylius 2.3, use $notAvailableMessage instead. It will be removed in Sylius 3.0. */
    public string $notAvailable = 'sylius.payment_method.not_available';

    /** @deprecated since Sylius 2.3, use $notExistMessage instead. It will be removed in Sylius 3.0. */
    public string $notExist = 'sylius.payment_method.not_exist';

    /** @deprecated since Sylius 2.3, use $paymentNotFoundMessage instead. It will be removed in Sylius 3.0. */
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
