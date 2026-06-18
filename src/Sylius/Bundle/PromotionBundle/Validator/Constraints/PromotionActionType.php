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

namespace Sylius\Bundle\PromotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class PromotionActionType extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $invalidTypeMessage = null,
        ?string $invalidType = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/promotion-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $invalidTypeMessage ??= $options['invalidTypeMessage'] ?? null;
            $invalidType ??= $options['invalidType'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $invalidType) {
            trigger_deprecation(
                'sylius/promotion-bundle',
                '2.3',
                'The "invalidType" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "invalidTypeMessage" instead.',
                static::class,
            );

            $invalidTypeMessage ??= $invalidType;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->invalidTypeMessage = $invalidTypeMessage ?? $this->invalidTypeMessage;
        $this->invalidType = $this->invalidTypeMessage;
    }

    public string $invalidTypeMessage = 'sylius.promotion_action.invalid_type';

    /** @deprecated since Sylius 2.3, use $invalidTypeMessage instead. It will be removed in Sylius 3.0. */
    public string $invalidType = 'sylius.promotion_action.invalid_type';

    public function validatedBy(): string
    {
        return 'sylius_promotion_action_type';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
