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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationTypeContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @param RepositoryInterface $productAssociationTypeRepository
     */
    public function __construct(RepositoryInterface $productAssociationTypeRepository)
    {
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
    }

    /**
     * @Transform /^association "([^"]+)"$/
     * @Transform :productAssociationType
     */
    public function getProductAssociationTypeByName($productAssociationTypeName)
    {
        $productAssociationType = $this->productAssociationTypeRepository->findOneBy([
            'name' => $productAssociationTypeName,
        ]);

        Assert::notNull(
            $productAssociationType,
            sprintf('Cannot find product association type with name %s', $productAssociationTypeName)
        );

        return $productAssociationType;
    }
}
