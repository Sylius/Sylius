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
final class ChosenPaymentRequestActionEligibility extends Constraint
{
    public const ACTION_NOT_AVAILABLE_ERROR = 'PAYMENT_REQUEST_ACTION_NOT_AVAILABLE';

    public const PAYMENT_METHOD_NOT_EXIST_ERROR = 'PAYMENT_METHOD_NOT_FOUND';

    /** @deprecated since Sylius 2.3, use $notAvailableMessage instead. It will be removed in Sylius 3.0. */
    public string $notAvailable = 'sylius.payment_request.action_not_available';

    /** @deprecated since Sylius 2.3, use $notExistMessage instead. It will be removed in Sylius 3.0. */
    public string $notExist = 'sylius.payment_method.not_exist';

    /** @deprecated since Sylius 2.3, use $notAllowedMessage instead. It will be removed in Sylius 3.0. */
    public string $notAllowed = 'sylius.payment_request.action_not_allowed';

    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $notAvailableMessage = 'sylius.payment_request.action_not_available',
        public string $notExistMessage = 'sylius.payment_method.not_exist',
        public string $notAllowedMessage = 'sylius.payment_request.action_not_allowed',
        ?string $notAvailable = null,
        ?string $notExist = null,
        ?string $notAllowed = null,
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
            $this->notAllowedMessage = $options['notAllowedMessage'] ?? $this->notAllowedMessage;
            $notAvailable ??= $options['notAvailable'] ?? null;
            $notExist ??= $options['notExist'] ?? null;
            $notAllowed ??= $options['notAllowed'] ?? null;
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

        if (null !== $notAllowed) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "notAllowed" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "notAllowedMessage" instead.',
                static::class,
            );

            $this->notAllowedMessage = $notAllowed;
        }

        parent::__construct(groups: $groups, payload: $payload);
        $this->notAvailable = $this->notAvailableMessage;
        $this->notExist = $this->notExistMessage;
        $this->notAllowed = $this->notAllowedMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_api_chosen_payment_request_action_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
