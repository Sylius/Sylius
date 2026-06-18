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
final class ChangedItemQuantityInCart extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $productNotExistMessage = null,
        ?string $productVariantNotLongerAvailableMessage = null,
        ?string $productVariantNotSufficientMessage = null,
        ?string $productVariantNotLongerAvailable = null,
        ?string $productVariantNotSufficient = null,
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

            $productNotExistMessage ??= $options['productNotExistMessage'] ?? null;
            $productVariantNotLongerAvailableMessage ??= $options['productVariantNotLongerAvailableMessage'] ?? null;
            $productVariantNotSufficientMessage ??= $options['productVariantNotSufficientMessage'] ?? null;
            $productVariantNotLongerAvailable ??= $options['productVariantNotLongerAvailable'] ?? null;
            $productVariantNotSufficient ??= $options['productVariantNotSufficient'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $productVariantNotLongerAvailable) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "productVariantNotLongerAvailable" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "productVariantNotLongerAvailableMessage" instead.',
                static::class,
            );

            $productVariantNotLongerAvailableMessage ??= $productVariantNotLongerAvailable;
        }

        if (null !== $productVariantNotSufficient) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "productVariantNotSufficient" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "productVariantNotSufficientMessage" instead.',
                static::class,
            );

            $productVariantNotSufficientMessage ??= $productVariantNotSufficient;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->productNotExistMessage = $productNotExistMessage ?? $this->productNotExistMessage;
        $this->productVariantNotLongerAvailableMessage = $productVariantNotLongerAvailableMessage ?? $this->productVariantNotLongerAvailableMessage;
        $this->productVariantNotSufficientMessage = $productVariantNotSufficientMessage ?? $this->productVariantNotSufficientMessage;
        $this->productVariantNotLongerAvailable = $this->productVariantNotLongerAvailableMessage;
        $this->productVariantNotSufficient = $this->productVariantNotSufficientMessage;
    }

    public string $productNotExistMessage = 'sylius.product.not_exist';

    public string $productVariantNotLongerAvailableMessage = 'sylius.product_variant.not_longer_available';

    public string $productVariantNotSufficientMessage = 'sylius.product_variant.not_sufficient';

    /** @deprecated since Sylius 2.3, use $productVariantNotLongerAvailableMessage instead. It will be removed in Sylius 3.0. */
    public string $productVariantNotLongerAvailable = 'sylius.product_variant.not_longer_available';

    /** @deprecated since Sylius 2.3, use $productVariantNotSufficientMessage instead. It will be removed in Sylius 3.0. */
    public string $productVariantNotSufficient = 'sylius.product_variant.not_sufficient';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_changed_item_quantity_in_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
