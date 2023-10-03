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

namespace Sylius\Component\Taxation\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @template T of TaxCategoryInterface
 *
 * @extends RepositoryInterface<T>
 */
interface TaxCategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|TaxCategoryInterface[]
     */
    public function findByName(string $name): array;
}
