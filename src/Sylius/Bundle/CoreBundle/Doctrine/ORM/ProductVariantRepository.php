<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductVariantRepository extends BaseProductVariantRepository implements ProductVariantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createInventoryListQueryBuilder()
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.tracked = true')
        ;
    }
}
