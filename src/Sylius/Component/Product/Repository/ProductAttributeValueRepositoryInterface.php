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

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductAttributeValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $choiceKey
     *
     * @return array|ProductAttributeValueInterface[]
     */
    public function findByJsonChoiceKey(string $choiceKey): array;
}
