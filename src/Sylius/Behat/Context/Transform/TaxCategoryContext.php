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
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxCategoryContext implements Context
{
    public function __construct(private TaxCategoryRepositoryInterface $taxCategoryRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" tax category$/
     * @Transform /^tax category "([^"]+)"$/
     * @Transform :taxCategory
     */
    public function getTaxCategoryByName($taxCategoryName)
    {
        $taxCategories = $this->taxCategoryRepository->findByName($taxCategoryName);

        Assert::eq(
            count($taxCategories),
            1,
            sprintf('%d tax categories has been found with name "%s".', count($taxCategories), $taxCategoryName),
        );

        return $taxCategories[0];
    }
}
