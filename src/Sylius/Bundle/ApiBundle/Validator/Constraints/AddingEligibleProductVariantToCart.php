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
final class AddingEligibleProductVariantToCart extends Constraint
{
    public const PRODUCT_NOT_EXIST_ERROR = 'PRODUCT_NOT_FOUND';

    public const PRODUCT_VARIANT_NOT_EXIST_ERROR = 'PRODUCT_VARIANT_NOT_FOUND';

    public const PRODUCT_VARIANT_NOT_SUFFICIENT_ERROR = 'INSUFFICIENT_STOCK';

    /** @deprecated since Sylius 2.3, use $productVariantNotSufficientMessage instead. It will be removed in Sylius 3.0. */
    public string $productVariantNotSufficient = 'sylius.product_variant.not_sufficient';

    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        public string $productNotExistMessage = 'sylius.product.not_exist',
        public string $productVariantNotExistMessage = 'sylius.product_variant.not_exist',
        public string $productVariantNotSufficientMessage = 'sylius.product_variant.not_sufficient',
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

            $this->productNotExistMessage = $options['productNotExistMessage'] ?? $this->productNotExistMessage;
            $this->productVariantNotExistMessage = $options['productVariantNotExistMessage'] ?? $this->productVariantNotExistMessage;
            $this->productVariantNotSufficientMessage = $options['productVariantNotSufficientMessage'] ?? $this->productVariantNotSufficientMessage;
            $productVariantNotSufficient ??= $options['productVariantNotSufficient'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        if (null !== $productVariantNotSufficient) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.3',
                'The "productVariantNotSufficient" option of the "%s" constraint is deprecated and will be removed in Sylius 3.0, use "productVariantNotSufficientMessage" instead.',
                static::class,
            );

            $this->productVariantNotSufficientMessage = $productVariantNotSufficient;
        }

        parent::__construct(groups: $groups, payload: $payload);
        $this->productVariantNotSufficient = $this->productVariantNotSufficientMessage;
    }

    public function validatedBy(): string
    {
        return 'sylius_api_validator_adding_eligible_product_variant_to_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
