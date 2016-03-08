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
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;

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
     * @var TaxCategoryRepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var ZoneRepositoryInterface
     */
    private $zoneRepository;

    /**
     * @param FactoryInterface $taxRateFactory
     * @param FactoryInterface $taxCategoryFactory
     * @param RepositoryInterface $taxRateRepository
     * @param TaxCategoryRepositoryInterface $taxCategoryRepository
     * @param ZoneRepositoryInterface $zoneRepository
     */
    public function __construct(
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxCategoryFactory,
        RepositoryInterface $taxRateRepository,
        TaxCategoryRepositoryInterface $taxCategoryRepository,
        ZoneRepositoryInterface $zoneRepository
    ) {
        $this->taxRateFactory = $taxRateFactory;
        $this->taxCategoryFactory = $taxCategoryFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within :zone zone
     * @Given /^the store has "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" for (the rest of the world)$/
     */
    public function storeHasTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, ZoneInterface $zone)
    {
        $taxCategory = $this->getOrCreateTaxCategory($taxCategoryName);

        $taxRate = $this->taxRateFactory->createNew();
        $taxRate->setName($taxRateName);
        $taxRate->setCode($this->getCodeFromNameAndZoneCode($taxRateName, $zone->getCode()));
        $taxRate->setZone($zone);
        $taxRate->setAmount($this->getAmountFromString($taxRateAmount));
        $taxRate->setCategory($taxCategory);
        $taxRate->setCalculator('default');

        $this->taxRateRepository->add($taxRate);
    }

    /**
     * @param string $taxCategoryName
     *
     * @return TaxCategoryInterface
     */
    private function getOrCreateTaxCategory($taxCategoryName)
    {
        $taxCategory = $this->taxCategoryRepository->findOneByName($taxCategoryName);
        if (null === $taxCategory) {
            $taxCategory = $this->createTaxCategory($taxCategoryName);
        }

        return $taxCategory;
    }

    /**
     * @param string $taxCategoryName
     *
     * @return TaxCategoryInterface
     */
    private function createTaxCategory($taxCategoryName)
    {
        $taxCategory = $this->taxCategoryFactory->createNew();
        $taxCategory->setName($taxCategoryName);
        $taxCategory->setCode($this->getCodeFromName($taxCategoryName));

        $this->taxCategoryRepository->add($taxCategory);

        return $taxCategory;
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

    /**
     * @param string $taxRateName
     * @param string $zoneCode
     *
     * @return string
     */
    private function getCodeFromNameAndZoneCode($taxRateName, $zoneCode)
    {
        return $this->getCodeFromName($taxRateName).'_'.strtolower($zoneCode);
    }
}
