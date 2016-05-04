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
use Sylius\Component\Variation\Repository\OptionRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductOptionContext implements Context
{
    /**
     * @var OptionRepositoryInterface
     */
    private $productOptionRepository;

    /**
     * @param OptionRepositoryInterface $productOptionRepository
     */
    public function __construct(OptionRepositoryInterface $productOptionRepository)
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
        $productOption = $this->productOptionRepository->findOneByName($productOptionName);
        Assert::notNull(
            $productOption,
            sprintf('Product option with name "%s" does not exist in the product option repository.', $productOptionName)
        );

        return $productOption;
    }
}
