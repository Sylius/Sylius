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
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @param RepositoryInterface $productOptionRepository
     */
    public function __construct(RepositoryInterface $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * @Transform /^product option "([^"]+)"$/
     * @Transform :productOption
     */
    public function getProductOptionByName($productOptionName)
    {
        $productOption = $this->productOptionRepository->findOneBy(['name' => $productOptionName]);
        if (null === $productOption) {
            throw new \InvalidArgumentException(
                sprintf('Product option with name "%s" does not exist in the product option repository.', $productOptionName)
            );
        }

        return $productOption;
    }
}
