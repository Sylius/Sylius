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

namespace Sylius\Component\Promotion\Repository;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of PromotionInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return PromotionInterface[]
     */
    public function findActive(): array;

    /**
     * @return PromotionInterface[]
     */
    public function findByName(string $name): array;
}
