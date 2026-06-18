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
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $productNotExistMessage = null,
        ?string $productVariantNotExistMessage = null,
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
            $productVariantNotExistMessage ??= $options['productVariantNotExistMessage'] ?? null;
            $productVariantNotSufficient ??= $options['productVariantNotSufficient'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->productNotExistMessage = $productNotExistMessage ?? $this->productNotExistMessage;
        $this->productVariantNotExistMessage = $productVariantNotExistMessage ?? $this->productVariantNotExistMessage;
        $this->productVariantNotSufficient = $productVariantNotSufficient ?? $this->productVariantNotSufficient;
    }

    public string $productNotExistMessage = 'sylius.product.not_exist';

    public string $productVariantNotExistMessage = 'sylius.product_variant.not_exist';

    public string $productVariantNotSufficient = 'sylius.product_variant.not_sufficient';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_adding_eligible_product_variant_to_cart';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
