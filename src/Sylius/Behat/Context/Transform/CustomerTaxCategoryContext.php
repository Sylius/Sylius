<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerTaxCategoryContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $customerTaxCategoryRepository;

    /**
     * @param RepositoryInterface $customerTaxCategoryRepository
     */
    public function __construct(RepositoryInterface $customerTaxCategoryRepository)
    {
        $this->customerTaxCategoryRepository = $customerTaxCategoryRepository;
    }

    /**
     * @Transform :customerTaxCategory
     * @Transform /^"([^"]+)" customer tax category$/
     */
    public function getCustomerTaxCategoryByName(string $name): CustomerTaxCategoryInterface
    {
        $customerTaxCategories = $this->customerTaxCategoryRepository->findByName($name);

        Assert::eq(
            count($customerTaxCategories),
            1,
            sprintf('%d customer tax categories has been found with name "%s".', count($customerTaxCategories), $name)
        );

        return $customerTaxCategories[0];
    }
}
