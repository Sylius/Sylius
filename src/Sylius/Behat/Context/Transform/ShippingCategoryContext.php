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
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShippingCategoryContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @param RepositoryInterface $shippingCategoryRepository
     */
    public function __construct(RepositoryInterface $shippingCategoryRepository)
    {
        $this->shippingCategoryRepository = $shippingCategoryRepository;
    }

    /**
     * @Transform /^"([^"]+)" shipping category/
     * @Transform /^shipping category "([^"]+)"/
     * @Transform /^shipping category with name "([^"]+)"$/
     * @Transform :shippingCategory
     */
    public function getShippingCategoryByName($shippingCategoryName)
    {
        $shippingCategories = $this->shippingCategoryRepository->findBy(['name' => $shippingCategoryName]);

        Assert::eq(
            count($shippingCategories),
            1,
            sprintf('%d shipping category has been found with name "%s".', count($shippingCategories), $shippingCategoryName)
        );

        return $shippingCategories[0];
    }
}
