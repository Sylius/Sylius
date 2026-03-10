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

    #[HasNamedArguments]
    public function __construct(
        string $message = 'sylius.cart_item.not_available',
        string $shortMessage = 'sylius.cart_item.insufficient_stock',
        string $stockablePath = 'stockable',
        string $quantityPath = 'quantity',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(groups: $groups, payload: $payload);

        $this->message = $message;
        $this->shortMessage = $shortMessage;
        $this->stockablePath = $stockablePath;
        $this->quantityPath = $quantityPath;
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
