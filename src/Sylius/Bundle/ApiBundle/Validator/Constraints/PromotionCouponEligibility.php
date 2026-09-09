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
final class PromotionCouponEligibility extends Constraint
{
    public const PROMOTION_COUPON_INVALID_ERROR = 'PROMOTION_COUPON_INVALID';

    public const PROMOTION_COUPON_EXPIRED_ERROR = 'PROMOTION_COUPON_EXPIRED';

    public const PROMOTION_COUPON_INELIGIBLE_ERROR = 'PROMOTION_COUPON_INELIGIBLE';

    protected const ERROR_NAMES = [
        self::PROMOTION_COUPON_INVALID_ERROR => 'PROMOTION_COUPON_INVALID_ERROR',
        self::PROMOTION_COUPON_EXPIRED_ERROR => 'PROMOTION_COUPON_EXPIRED_ERROR',
        self::PROMOTION_COUPON_INELIGIBLE_ERROR => 'PROMOTION_COUPON_INELIGIBLE_ERROR',
    ];

    /** @deprecated since Sylius 2.3, use $invalidMessage instead. It will be removed in Sylius 3.0. */
    public string $message = 'sylius.promotion_coupon.is_invalid';

    /**
     * @param array<string, mixed>|null $options
     * @param array<string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $invalidMessage = 'sylius.promotion_coupon.is_invalid',
        public string $expiredMessage = 'sylius.promotion_coupon.is_expired',
        public string $ineligibleMessage = 'sylius.promotion_coupon.is_ineligible',
        ?string $message = null,
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

            $this->invalidMessage = $options['invalidMessage'] ?? $this->invalidMessage;
            $this->expiredMessage = $options['expiredMessage'] ?? $this->expiredMessage;
            $this->ineligibleMessage = $options['ineligibleMessage'] ?? $this->ineligibleMessage;
            $message ??= $options['message'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $message) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "message" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "invalidMessage" instead.',
                static::class,
            );

            $this->invalidMessage = $message;
        }

        parent::__construct(groups: $groups, payload: $payload);
        $this->message = $this->invalidMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_api_promotion_coupon_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
