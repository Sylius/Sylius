<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $taxRateFactory;

    /**
     * @var FactoryInterface
     */
    private $taxCategoryFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @param FactoryInterface $taxRateFactory
     * @param FactoryInterface $taxCategoryFactory
     * @param RepositoryInterface $taxRateRepository
     * @param RepositoryInterface $taxCategoryRepository
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxCategoryFactory,
        RepositoryInterface $taxRateRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $zoneRepository
    ) {
        $this->taxRateFactory = $taxRateFactory;
        $this->taxCategoryFactory = $taxCategoryFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * @Transform /^"([^"]+)" tax category$/
     * @Transform /^tax category "([^"]+)"$/
     */
    public function castTaxCategoryNameToTaxCategory($taxCategoryName)
    {
        $taxCategory = $this->taxCategoryRepository->findOneBy(['name' => $taxCategoryName]);
        if (null === $taxCategory) {
            throw new \Exception('Tax category with name "'.$taxCategoryName.'" does not exist');
        }

        return $taxCategory;
    }

    /**
     * @Given /^store has "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" within ("([^"]+)" zone)$/
     */
    public function storeHasTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, ZoneInterface $taxZone)
    {
        $taxCategory = $this->taxCategoryFactory->createNew();
        $taxCategory->setName($taxCategoryName);
        $taxCategory->setCode($this->getCodeFromName($taxCategoryName));

        $this->taxCategoryRepository->add($taxCategory);

        $taxRate = $this->taxRateFactory->createNew();
        $taxRate->setName($taxRateName);
        $taxRate->setCode($this->getCodeFromName($taxRateName));
        $taxRate->setZone($taxZone);
        $taxRate->setAmount($this->getAmountFromString($taxRateAmount));
        $taxRate->setCategory($taxCategory);
        $taxRate->setCalculator('default');

        $this->taxRateRepository->add($taxRate);
    }

    /**
     * @param string $taxRateAmount
     *
     * @return string
     */
    private function getAmountFromString($taxRateAmount)
    {
        return ((int) $taxRateAmount) / 100;
    }

    /**
     * @param string $taxRateName
     *
     * @return string
     */
    private function getCodeFromName($taxRateName)
    {
        return str_replace(' ', '_', strtolower($taxRateName));
    }
}
