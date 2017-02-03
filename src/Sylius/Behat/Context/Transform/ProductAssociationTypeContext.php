<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationTypeContext implements Context
{
    /**
     * @var ProductAssociationTypeRepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @param ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository
     */
    public function __construct(ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository)
    {
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
    }

    /**
     * @Transform /^association "([^"]+)"$/
     * @Transform :productAssociationType
     */
    public function getProductAssociationTypeByName($productAssociationTypeName)
    {
        $productAssociationTypes = $this->productAssociationTypeRepository->findByName(
            $productAssociationTypeName,
            'en_US'
        );

        Assert::eq(
            count($productAssociationTypes),
            1,
            sprintf(
                '%d product association types has been found with name "%s".',
                count($productAssociationTypes),
                $productAssociationTypeName
            )
        );

        return $productAssociationTypes[0];
    }
}
