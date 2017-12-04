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

namespace Sylius\Component\Product\Repository;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return array|ProductInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @param string $phrase
     * @param string $locale
     *
     * @return array|ProductInterface[]
     */
    public function findByNamePart(string $phrase, string $locale): array;
}
