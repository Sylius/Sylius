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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductAssociationTypeContext implements Context
{
    public function __construct(private ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository)
    {
    }

    /**
     * @Transform /^association "([^"]+)"$/
     * @Transform /^associate as "([^"]+)"$/
     * @Transform :productAssociationType
     */
    public function getProductAssociationTypeByName($productAssociationTypeName)
    {
        $productAssociationTypes = $this->productAssociationTypeRepository->findByName(
            $productAssociationTypeName,
            'en_US',
        );

        Assert::eq(
            count($productAssociationTypes),
            1,
            sprintf(
                '%d product association types has been found with name "%s".',
                count($productAssociationTypes),
                $productAssociationTypeName,
            ),
        );

        return $productAssociationTypes[0];
    }
}
