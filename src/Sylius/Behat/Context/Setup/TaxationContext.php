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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Repository\TaxCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxationContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $taxRateFactory,
        private FactoryInterface $taxCategoryFactory,
        private RepositoryInterface $taxRateRepository,
        private TaxCategoryRepositoryInterface $taxCategoryRepository,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone with dates between :startDate and :endDate
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone identified by the :taxRateCode code
     * @Given /^the store has(?:| also) "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" for the (rest of the world)$/
     */
    public function storeHasTaxRateWithinZone(
        $taxRateName,
        $taxRateAmount,
        $taxCategoryName,
        ZoneInterface $zone,
        $taxRateCode = null,
        $includedInPrice = false,
        ?string $startDate = null,
        ?string $endDate = null,
    ) {
        $this->configureTaxRate(
            $taxCategoryName,
            $taxRateCode,
            $taxRateName,
            $zone,
            $taxRateAmount,
            $includedInPrice,
            $startDate !== null ? new \DateTime($startDate) : null,
            $endDate !== null ? new \DateTime($endDate) : null,
        );
    }

    /**
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone ending at :endDate
     */
    public function storeHasTaxRateWithinZoneEndingAt(
        string $taxRateName,
        string $taxRateAmount,
        string $taxCategoryName,
        ZoneInterface $zone,
        string $endDate,
    ) {
        $this->configureTaxRate($taxCategoryName, null, $taxRateName, $zone, $taxRateAmount, false, null, new \DateTime($endDate));
    }

    /**
     * @Given the store has :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone starting at :startDate
     */
    public function storeHasTaxRateWithinZoneStartingAt(
        string $taxRateName,
        string $taxRateAmount,
        string $taxCategoryName,
        ZoneInterface $zone,
        string $startDate,
    ) {
        $this->configureTaxRate($taxCategoryName, StringInflector::nameToCode($taxRateName), $taxRateName, $zone, $taxRateAmount, false, new \DateTime($startDate));
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
     * @Given the store has tax categories :firstName, :secondName and :thirdName
     */
    public function theStoreHasTaxCategories(string ...$names): void
    {
        foreach ($names as $name) {
            $this->theStoreHasTaxCategoryWithCode($name);
        }
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
        $taxRate->setAmount((float) $this->getAmountFromString($amount));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this tax rate) operates between "([^"]+)" and "([^"]+)"$/
     */
    public function theTaxRateOperatesBetweenDates(
        TaxRateInterface $taxRate,
        string $startDate,
        string $endDate,
    ): void {
        $taxRate->setStartDate(new \DateTime($startDate));
        $taxRate->setEndDate(new \DateTime($endDate));
        $this->objectManager->flush();
    }

    /**
     * @Given the :taxRate tax rate has :calculator calculator configured
     */
    public function theTaxRateHasCalculatorConfigured(TaxRateInterface $taxRate, string $calculator): void
    {
        $taxRate->setCalculator($calculator);

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
            sprintf('%d tax categories has been found with name "%s".', count($taxCategories), $taxCategoryName),
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
        return $this->getCodeFromName($taxRateName) . '_' . strtolower($zoneCode);
    }

    private function configureTaxRate(
        string $taxCategoryName,
        ?string $taxRateCode,
        string $taxRateName,
        ZoneInterface $zone,
        string $taxRateAmount,
        bool $includedInPrice,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
    ): void {
        $taxCategory = $this->getOrCreateTaxCategory($taxCategoryName);

        if (null === $taxRateCode) {
            $taxRateCode = $this->getCodeFromNameAndZoneCode($taxRateName, $zone->getCode());
        }

        /** @var TaxRateInterface $taxRate */
        $taxRate = $this->taxRateFactory->createNew();
        $taxRate->setName($taxRateName);
        $taxRate->setCode($taxRateCode);
        $taxRate->setZone($zone);
        $taxRate->setAmount((float) $this->getAmountFromString($taxRateAmount));
        $taxRate->setCategory($taxCategory);
        $taxRate->setCalculator('default');
        $taxRate->setIncludedInPrice($includedInPrice);
        $taxRate->setStartDate($startDate);
        $taxRate->setEndDate($endDate);

        $this->taxRateRepository->add($taxRate);

        $this->sharedStorage->set('tax_rate', $taxRate);
    }
}
