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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsEntityObserver;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;

#[AsEntityObserver(priority: 5)]
final class EntityObserverStub implements EntityObserverInterface
{
    public function onChange(object $entity): void
    {
    }

    public function supports(object $entity): bool
    {
        return true;
    }

    public function observedFields(): array
    {
        return [];
    }
}
