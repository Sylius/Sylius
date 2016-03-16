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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class TaxContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @param RepositoryInterface $taxCategoryRepository
     */
    public function __construct(RepositoryInterface $taxCategoryRepository)
    {
        $this->taxCategoryRepository = $taxCategoryRepository;
    }

    /**
     * @Transform /^"([^"]+)" tax category$/
     * @Transform /^tax category "([^"]+)"$/
     * @Transform :taxCategory tax category
     */
    public function getTaxCategoryByName($taxCategoryName)
    {
        $taxCategory = $this->taxCategoryRepository->findOneBy(['name' => $taxCategoryName]);
        if (null === $taxCategory) {
            throw new \InvalidArgumentException('Tax category with name "'.$taxCategoryName.'" does not exist');
        }

        return $taxCategory;
    }
}
