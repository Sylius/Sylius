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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxationContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

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
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $taxRateFactory
     * @param FactoryInterface $taxCategoryFactory
     * @param RepositoryInterface $taxRateRepository
     * @param TaxCategoryRepositoryInterface $taxCategoryRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxCategoryFactory,
        RepositoryInterface $taxRateRepository,
        TaxCategoryRepositoryInterface $taxCategoryRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxRateFactory = $taxRateFactory;
        $this->taxCategoryFactory = $taxCategoryFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone identified by the :taxRateCode code
     * @Given /^the store has "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" for the (rest of the world)$/
     */
    public function storeHasTaxRateWithinZone(
        $taxRateName,
        $taxRateAmount,
        $taxCategoryName,
        ZoneInterface $zone,
        $taxRateCode = null,
        $includedInPrice = false
    ) {
        $taxCategory = $this->getOrCreateTaxCategory($taxCategoryName);

        if (null === $taxRateCode) {
            $taxRateCode = $this->getCodeFromNameAndZoneCode($taxRateName, $zone->getCode());
        }

        /** @var TaxRateInterface $taxRate */
        $taxRate = $this->taxRateFactory->createNew();
        $taxRate->setName($taxRateName);
        $taxRate->setCode($taxRateCode);
        $taxRate->setZone($zone);
        $taxRate->setAmount($this->getAmountFromString($taxRateAmount));
        $taxRate->setCategory($taxCategory);
        $taxRate->setCalculator('default');
        $taxRate->setIncludedInPrice($includedInPrice);

        $this->taxRateRepository->add($taxRate);

        $this->sharedStorage->set('tax_rate', $taxRate);
    }

    /**
     * @Given the store has included in price :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone
     */
    public function storeHasIncludedInPriceTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, ZoneInterface $zone)
    {
        $this->storeHasTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, $zone, null, true);
    }

    /**
     * @Given the store has a tax category :name with a code :code
     * @Given the store has a tax category :name
     * @Given the store has a tax category :name also
     */
    public function theStoreHasTaxCategoryWithCode($name, $code = null)
    {
        $taxCategory = $this->createTaxCategory($name, $code);

        $this->sharedStorage->set('tax_category', $taxCategory);
    }

    /**
     * @Given the store does not have any categories defined
     */
    public function theStoreDoesNotHaveAnyCategoriesDefined()
    {
        $taxCategories = $this->taxCategoryRepository->findAll();

        foreach ($taxCategories as $taxCategory) {
            $this->taxCategoryRepository->remove($taxCategory);
        }
    }

    /**
     * @Given /^the ("[^"]+" tax rate) has changed to ([^"]+)%$/
     */
    public function theTaxRateIsOfAmount(TaxRateInterface $taxRate, $amount)
    {
        $taxRate->setAmount($this->getAmountFromString($amount));

        $this->objectManager->flush();
    }

    /**
     * @param string $taxCategoryName
     *
     * @return TaxCategoryInterface
     */
    private function getOrCreateTaxCategory($taxCategoryName)
    {
        $taxCategories = $this->taxCategoryRepository->findByName($taxCategoryName);
        if (empty($taxCategories)) {
            return $this->createTaxCategory($taxCategoryName);
        }

        Assert::eq(
            count($taxCategories),
            1,
            sprintf('%d tax categories has been found with name "%s".', count($taxCategories), $taxCategoryName)
        );

        return $taxCategories[0];
    }

    /**
     * @param string $taxCategoryName
     * @param string|null $taxCategoryCode
     *
     * @return TaxCategoryInterface
     */
    private function createTaxCategory($taxCategoryName, $taxCategoryCode = null)
    {
        /** @var TaxCategoryInterface $taxCategory */
        $taxCategory = $this->taxCategoryFactory->createNew();
        if (null === $taxCategoryCode) {
            $taxCategoryCode = $this->getCodeFromName($taxCategoryName);
        }

        $taxCategory->setName($taxCategoryName);
        $taxCategory->setCode($taxCategoryCode);

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
        return StringInflector::nameToLowercaseCode($taxRateName);
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
