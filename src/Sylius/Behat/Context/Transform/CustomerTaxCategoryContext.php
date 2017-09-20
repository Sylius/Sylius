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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerTaxCategoryContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $customerTaxCategoryRepository;

    /**
     * @var FactoryInterface
     */
    private $customerTaxCategoryFactory;

    /**
     * @param RepositoryInterface $customerTaxCategoryRepository
     * @param FactoryInterface $customerTaxCategoryFactory
     */
    public function __construct(
        RepositoryInterface $customerTaxCategoryRepository,
        FactoryInterface $customerTaxCategoryFactory
    ) {
        $this->customerTaxCategoryRepository = $customerTaxCategoryRepository;
        $this->customerTaxCategoryFactory = $customerTaxCategoryFactory;
    }

    /**
     * @Transform :customerTaxCategory
     * @Transform /^"([^"]+)" customer tax category$/
     */
    public function getOrCreateCustomerTaxCategoryByName(string $name): CustomerTaxCategoryInterface
    {
        $customerTaxCategory = $this->customerTaxCategoryRepository->findOneBy(['name' => $name]);
        if (null === $customerTaxCategory) {
            /** @var CustomerTaxCategoryInterface $customerTaxCategory */
            $customerTaxCategory = $this->customerTaxCategoryFactory->createNew();
            $customerTaxCategory->setName($name);
            $customerTaxCategory->setCode(StringInflector::nameToCode($name));

            $this->customerTaxCategoryRepository->add($customerTaxCategory);
        }

        return $customerTaxCategory;
    }
}
