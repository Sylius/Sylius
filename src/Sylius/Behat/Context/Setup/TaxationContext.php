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
     * @Given /^the store has(?:| also) "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" for the (rest of the world)$/
     */
    public function storeHasTaxRateWithinZone(
        $taxRateName,
        $taxRateAmount,
        $taxCategoryName,
        ZoneInterface $zone,
        $taxRateCode = null,
        $includedInPrice = false
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

        $this->taxRateRepository->add($taxRate);

        $this->sharedStorage->set('tax_rate', $taxRate);
    }

    /**
     * @Given the store has included in price :taxRateName tax rate of :taxRateAmount% for :taxCategoryName within the :zone zone
     */
    public function storeHasIncludedInPriceTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, ZoneInterface $zone): void
    {
        $this->storeHasTaxRateWithinZone($taxRateName, $taxRateAmount, $taxCategoryName, $zone, null, true);
    }

    /**
     * @Given the store has a tax category :name with a code :code
     * @Given the store has a tax category :name
     * @Given the store has a tax category :name also
     */
    public function theStoreHasTaxCategoryWithCode($name, $code = null): void
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
    public function theStoreDoesNotHaveAnyCategoriesDefined(): void
    {
        $taxCategories = $this->taxCategoryRepository->findAll();

        foreach ($taxCategories as $taxCategory) {
            $this->taxCategoryRepository->remove($taxCategory);
        }
    }

    /**
     * @Given /^the ("[^"]+" tax rate) has changed to ([^"]+)%$/
     */
    public function theTaxRateIsOfAmount(TaxRateInterface $taxRate, $amount): void
    {
        $taxRate->setAmount((float) $this->getAmountFromString($amount));

        $this->objectManager->flush();
    }

    private function getOrCreateTaxCategory(string $taxCategoryName): TaxCategoryInterface
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

    private function createTaxCategory(string $taxCategoryName, string $taxCategoryCode = null): TaxCategoryInterface
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

    private function getAmountFromString(string $taxRateAmount): string
    {
        return ((int) $taxRateAmount) / 100;
    }

    private function getCodeFromName(string $taxRateName): string
    {
        return StringInflector::nameToLowercaseCode($taxRateName);
    }

    private function getCodeFromNameAndZoneCode(string $taxRateName, string $zoneCode): string
    {
        return $this->getCodeFromName($taxRateName) . '_' . strtolower($zoneCode);
    }
}
