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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Product\Model\ProductAssociationInterface;

final class ProductAssociationUpdater implements ProductAssociationUpdaterInterface
{
    public function update(ProductAssociationInterface $productAssociation, array $attributes): void
    {
        $productAssociation->setType($attributes['type']);
        $productAssociation->setOwner($attributes['owner']);

        foreach ($attributes['associated_products'] as $product) {
            $productAssociation->addAssociatedProduct($product);
        }
    }
}
