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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
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
     * @var FactoryInterface
     */
    private $customerTaxCategoryFactory;

    /**
     * @var RepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @var TaxCategoryRepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerTaxCategoryRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $taxRateFactory
     * @param FactoryInterface $taxCategoryFactory
     * @param FactoryInterface $customerTaxCategoryFactory
     * @param RepositoryInterface $taxRateRepository
     * @param TaxCategoryRepositoryInterface $taxCategoryRepository
     * @param RepositoryInterface $customerTaxCategoryRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxCategoryFactory,
        FactoryInterface $customerTaxCategoryFactory,
        RepositoryInterface $taxRateRepository,
        TaxCategoryRepositoryInterface $taxCategoryRepository,
        RepositoryInterface $customerTaxCategoryRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxRateFactory = $taxRateFactory;
        $this->taxCategoryFactory = $taxCategoryFactory;
        $this->customerTaxCategoryFactory = $customerTaxCategoryFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->customerTaxCategoryRepository = $customerTaxCategoryRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the store has (also) a :taxRateName tax rate of :taxRateAmount% for :taxCategoryName and :customerTaxCategory customer tax category within the :zone zone
     * @Given the store has (also) a :taxRateName tax rate of :taxRateAmount% for :taxCategoryName and :customerTaxCategory customer tax category within the :zone zone identified by the :taxRateCode code
     * @Given /^the store has(?:| also) a "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" and ("[^"]+" customer tax category) for the (rest of the world)$/
     */
    public function theStoreHasTaxRateOfForTaxCategoryAndCustomerTaxCategoryWithinTheZone(
        string $taxRateName,
        int $taxRateAmount,
        string $taxCategoryName,
        CustomerTaxCategoryInterface $customerTaxCategory,
        ZoneInterface $zone,
        ?string $taxRateCode = null,
        bool $includedInPrice = false
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
        $taxRate->setCustomerTaxCategory($customerTaxCategory);
        $taxRate->setAmount((float) $this->getAmountFromString($taxRateAmount));
        $taxRate->setCategory($taxCategory);
        $taxRate->setCalculator('default');
        $taxRate->setIncludedInPrice($includedInPrice);

        $this->taxRateRepository->add($taxRate);

        $this->sharedStorage->set('tax_rate', $taxRate);
    }

    /**
     * @Given the store has (also) included in price :taxRateName tax rate of :taxRateAmount% for :taxCategoryName and :customerTaxCategory customer tax category within the :zone zone
     * @Given /^the store has(?:| also) included in price "([^"]+)" tax rate of ([^"]+)% for "([^"]+)" and ("[^"]+" customer tax category) for the (rest of the world)$/
     */
    public function storeHasIncludedInPriceTaxRateWithinZone(
        string $taxRateName,
        int $taxRateAmount,
        string $taxCategoryName,
        CustomerTaxCategoryInterface $customerTaxCategory,
        ZoneInterface $zone
    ): void {
        $this->theStoreHasTaxRateOfForTaxCategoryAndCustomerTaxCategoryWithinTheZone(
            $taxRateName,
            $taxRateAmount,
            $taxCategoryName,
            $customerTaxCategory,
            $zone,
            null,
            true
        );
    }

    /**
     * @Given the store has a tax category :name with a code :code
     * @Given the store has (also) a tax category :name
     */
    public function theStoreHasTaxCategoryWithCode(string $name, ?string $code = null): void
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
        $taxRate->setAmount((float) $this->getAmountFromString($amount));

        $this->objectManager->flush();
    }

    /**
     * @Given the store has a customer tax category :name with a code :code
     * @Given the store has (also) a customer tax category :name
     */
    public function theStoreHasACustomerTaxCategoryWithACode(string $name, ?string $code = null): void
    {
        $customerTaxCategory = $this->createCustomerTaxCategory($name, $code);

        $this->sharedStorage->set('customer_tax_category', $customerTaxCategory);
    }

    /**
     * @Given the store has (also) customer tax categories :firstName and :secondName
     */
    public function theStoreHasCustomerTaxCategories(...$names): void
    {
        foreach ($names as $name) {
            $this->theStoreHasACustomerTaxCategoryWithACode($name);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this customer tax category) has a description specified as "([^"]+)"$/
     */
    public function thisCustomerTaxCategoryHasADescriptionSpecifiedAs(
        CustomerTaxCategoryInterface $customerTaxCategory,
        string $description
    ): void {
        $customerTaxCategory->setDescription($description);

        $this->objectManager->flush();
    }

    /**
     * @Given the tax rate :taxRate is applicable for the :customerTaxCategory customer tax category
     */
    public function theTaxRateIsApplicableForTheCustomerTaxCategory(
        TaxRateInterface $taxRate,
        CustomerTaxCategoryInterface $customerTaxCategory
    ): void {
        $taxRate->setCustomerTaxCategory($customerTaxCategory);

        $this->objectManager->flush();
    }

    /**
     * @Given default customer tax category is :customerTaxCategory
     */
    public function defaultCustomerTaxCategoryIs(CustomerTaxCategoryInterface $customerTaxCategory): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setDefaultCustomerTaxCategory($customerTaxCategory);

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
     * @param string $name
     * @param string|null $code
     *
     * @return CustomerTaxCategoryInterface
     */
    private function createCustomerTaxCategory(string $name, ?string $code = null): CustomerTaxCategoryInterface
    {
        /** @var CustomerTaxCategoryInterface $customerTaxCategory */
        $customerTaxCategory = $this->customerTaxCategoryFactory->createNew();

        if (null === $code) {
            $code = $this->getCodeFromName($name);
        }

        $customerTaxCategory->setName($name);
        $customerTaxCategory->setCode($code);

        $this->customerTaxCategoryRepository->add($customerTaxCategory);

        return $customerTaxCategory;
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
}
