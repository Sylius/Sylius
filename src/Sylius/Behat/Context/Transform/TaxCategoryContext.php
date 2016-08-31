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
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class TaxCategoryContext implements Context
{
    /**
     * @var TaxCategoryRepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @param TaxCategoryRepositoryInterface $taxCategoryRepository
     */
    public function __construct(TaxCategoryRepositoryInterface $taxCategoryRepository)
    {
        $this->taxCategoryRepository = $taxCategoryRepository;
    }

    /**
     * @Transform /^"([^"]+)" tax category$/
     * @Transform /^tax category "([^"]+)"$/
     * @Transform :taxCategory
     */
    public function getTaxCategoryByName($taxCategoryName)
    {
        $taxCategory = $this->taxCategoryRepository->findOneByName($taxCategoryName);

        Assert::notNull(
            $taxCategory,
            sprintf('Tax category with name "%s" does not exist', $taxCategoryName)
        );

        return $taxCategory;
    }
}
