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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductOptionValueDeletionListener
{
    /**
     * @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository
     */
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
    ) {
    }

    public function preRemove(ProductOptionValueInterface $optionValue): void
    {
        if ($this->productVariantRepository->countByProductOptionValueId($optionValue->getId()) > 0) {
            throw new ResourceDeleteException('product option value');
        }
    }
}
