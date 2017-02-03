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
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionContext implements Context
{
    /**
     * @var ProductOptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @param ProductOptionRepositoryInterface $productOptionRepository
     */
    public function __construct(ProductOptionRepositoryInterface $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * @Transform /^product option "([^"]+)"$/
     * @Transform /^"([^"]+)" option$/
     * @Transform :productOption
     */
    public function getProductOptionByName($productOptionName)
    {
        $productOptions = $this->productOptionRepository->findByName($productOptionName, 'en_US');

        Assert::eq(
            count($productOptions),
            1,
            sprintf('%d product options has been found with name "%s".', count($productOptions), $productOptionName)
        );

        return $productOptions[0];
    }
}
