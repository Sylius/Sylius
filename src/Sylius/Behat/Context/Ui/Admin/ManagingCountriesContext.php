<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Country\CreatePageInterface;
use Sylius\Behat\Page\Admin\Country\IndexPageInterface;
use Sylius\Behat\Page\Admin\Country\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingCountriesContext implements Context
{
    const RESOURCE_NAME = 'country';

    /**
     * @var IndexPageInterface
     */
    private $countryIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $countryCreatePage;

    /**
     * @var UpdatePageInterface
     */
    private $countryUpdatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $countryIndexPage
     * @param CreatePageInterface $countryCreatePage
     * @param UpdatePageInterface $countryUpdatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $countryIndexPage,
        CreatePageInterface $countryCreatePage,
        UpdatePageInterface $countryUpdatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->countryIndexPage = $countryIndexPage;
        $this->countryCreatePage = $countryCreatePage;
        $this->countryUpdatePage = $countryUpdatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to add a new country
     * @Given I want to add a new country with a province
     */
    public function iWantToAddNewCountry()
    {
        $this->countryCreatePage->open();
    }

    /**
     * @Given /^I want to edit (this country)$/
     */
    public function iWantToEditThisCountry(CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);
    }

    /**
     * @When I choose :countryName
     */
    public function iChoose($countryName)
    {
        $this->countryCreatePage->chooseName($countryName);
    }

    /**
     * @When I add the :provinceName province with :provinceCode code
     * @When I add the :provinceName province with :provinceCode code and :provinceAbbreviation abbreviation
     */
    public function iAddProvinceWithCode($provinceName, $provinceCode, $provinceAbbreviation = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->countryCreatePage, $this->countryUpdatePage);

        $currentPage->addProvince($provinceName, $provinceCode, $provinceAbbreviation);
    }

    /**
     * @When I add it
     * @When I add this country
     */
    public function iAddIt()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->countryCreatePage, $this->countryUpdatePage);

        $currentPage->create();
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->countryUpdatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->countryUpdatePage->disable();
    }

    /**
     * @When I save my changes
     * @When I try to save changes
     */
    public function iSaveMyChanges()
    {
        $this->countryUpdatePage->saveChanges();
    }

    /**
     * @Then I should be notified about successful creation
     */
    public function iShouldBeNotifiedAboutSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then /^the (country "([^"]+)") should appear in the store$/
     */
    public function countryShouldAppearInTheStore(CountryInterface $country)
    {
        Assert::true(
            $this->countryUpdatePage->isOpen(['id' => $country->getId()]),
            sprintf('Country %s does not appear in the store.', $country->getCode())
        );
    }

    /**
     * @Then /^(this country) should be enabled$/
     */
    public function thisCountryShouldBeEnabled(CountryInterface $country)
    {
        $this->countryIndexPage->open();

        Assert::true(
            $this->countryIndexPage->isCountryEnabled($country),
            sprintf('Country %s should be enabled but it is not', $country->getCode())
        );
    }

    /**
     * @Then /^(this country) should be disabled$/
     */
    public function thisCountryShouldBeDisabled(CountryInterface $country)
    {
        $this->countryIndexPage->open();

        Assert::true(
            $this->countryIndexPage->isCountryDisabled($country),
            sprintf('Country %s should be disabled but it is not', $country->getCode())
        );
    }

    /**
     * @Then /^I should not be able to choose "([^"]*)"$/
     */
    public function iShouldNotBeAbleToChoose($name)
    {
        expect($this->countryCreatePage)->toThrow(ElementNotFoundException::class)->during('chooseName', [$name]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->countryUpdatePage->isCodeFieldDisabled(),
            'Code field should be disabled but is not'
        );
    }

    /**
     * @Then this country should have the :provinceName province
     */
    public function countryShouldHaveProvince($provinceName)
    {
        Assert::true(
            $this->countryUpdatePage->isThereProvince($provinceName),
            sprintf('%s is not a province of this country.', $provinceName)
        );
    }

    /**
     * @Then this country should not have the :provinceName province
     */
    public function thisCountryShouldNotHaveTheProvince($provinceName)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->countryCreatePage, $this->countryUpdatePage);

        Assert::false(
            $currentPage->isThereProvince($provinceName),
            sprintf('%s is a province of this country.', $provinceName)
        );
    }

    /**
     * @Then /^the province should still be named "([^"]*)" in (this country)$/
     */
    public function thisProvinceShouldStillBeNamed($provinceName, CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);

        Assert::true(
            $this->countryUpdatePage->isThereProvince($provinceName),
            sprintf('%s is not a province of this country.', $provinceName)
        );
    }

    /**
     * @Then /^province with name "([^"]*)" should not be added in (this country)$/
     */
    public function provinceWithNameShouldNotBeAdded($provinceName, CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);

        Assert::false(
            $this->countryUpdatePage->isThereProvince($provinceName),
            sprintf('%s is a province of this country.', $provinceName)
        );
    }

    /**
     * @Then /^province with code "([^"]*)" should not be added in (this country)$/
     */
    public function provinceWithCodeShouldNotBeAdded($provinceCode, CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);

        Assert::false(
            $this->countryUpdatePage->isThereProvinceWithCode($provinceCode),
            sprintf('%s is a province of this country.', $provinceCode)
        );
    }

    /**
     * @When /^I delete the "([^"]*)" province of (this country)$/
     */
    public function iDeleteTheProvinceOfCountry($provinceName, CountryInterface $country)
    {
        $this->countryUpdatePage->removeProvince($provinceName);
    }

    /**
     * @Given /^I want to create a new province in (country "([^"]*)")$/
     */
    public function iWantToCreateANewProvinceInCountry(CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);

        $this->countryUpdatePage->clickAddProvinceButton();
    }

    /**
     * @When /^I name the province "([^"]*)"$/
     * @When /^I do not name the province$/
     */
    public function iNameTheProvince($provinceName = null)
    {
        $this->countryUpdatePage->nameProvince($provinceName);
    }

    /**
     * @When I do not specify the province code
     * @When I specify the province code as :provinceCode
     */
    public function iSpecifyTheProvinceCode($provinceCode = null)
    {
        $this->countryUpdatePage->specifyProvinceCode($provinceCode);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter province %s.', $element));
    }

    /**
     * @When /^I remove "([^"]*)" province name$/
     */
    public function iRemoveProvinceName($provinceName)
    {
        $this->countryUpdatePage->removeProvinceName($provinceName);
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        Assert::true(
            $this->countryUpdatePage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Province %s should be required.', $element)
        );
    }

    /**
     * @Then /^I should be notified that province code must be unique$/
     */
    public function iShouldBeNotifiedThatProvinceCodeMustBeUnique()
    {
        Assert::true(
            $this->countryUpdatePage->checkValidationMessageFor('code', 'Province code must be unique.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }
}
