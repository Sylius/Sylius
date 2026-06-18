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

namespace Sylius\Bundle\InventoryBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class InStock extends Constraint
{
    /**
     * @param array<string, mixed>|null $options
     */
    #[HasNamedArguments]
    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?string $shortMessage = null,
        ?string $stockablePath = null,
        ?string $quantityPath = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($options)) {
            trigger_deprecation(
                'sylius/inventory-bundle',
                '2.3',
                'Passing an array of options to configure the "%s" constraint is deprecated and will be removed in Sylius 3.0, use named arguments instead.',
                static::class,
            );

            $message ??= $options['message'] ?? null;
            $shortMessage ??= $options['shortMessage'] ?? null;
            $stockablePath ??= $options['stockablePath'] ?? null;
            $quantityPath ??= $options['quantityPath'] ?? null;
            $groups ??= $options['groups'] ?? null;
            $payload ??= $options['payload'] ?? null;
        }

        parent::__construct(groups: $groups, payload: $payload);

        $this->message = $message ?? $this->message;
        $this->shortMessage = $shortMessage ?? $this->shortMessage;
        $this->stockablePath = $stockablePath ?? $this->stockablePath;
        $this->quantityPath = $quantityPath ?? $this->quantityPath;
    }

    public string $message = 'sylius.cart_item.not_available';

    public string $shortMessage = 'sylius.cart_item.insufficient_stock';

    public string $stockablePath = 'stockable';

    public string $quantityPath = 'quantity';

    public function validatedBy(): string
    {
        return 'sylius_in_stock';
    }

    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }
}
