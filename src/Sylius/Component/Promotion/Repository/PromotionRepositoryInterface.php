<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Repository;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return PromotionInterface[]
     */
    public function findActive(): array;

    /**
     * @param string $name
     *
     * @return PromotionInterface[]
     */
    public function findByName(string $name): array;
}
