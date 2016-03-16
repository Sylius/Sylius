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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $countryIndexPage
     * @param CreatePageInterface $countryCreatePage
     * @param UpdatePageInterface $countryUpdatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $countryIndexPage,
        CreatePageInterface $countryCreatePage,
        UpdatePageInterface $countryUpdatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->countryIndexPage = $countryIndexPage;
        $this->countryCreatePage = $countryCreatePage;
        $this->countryUpdatePage = $countryUpdatePage;
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
     * @When /^I choose "([^"]*)"$/
     */
    public function iChoose($name)
    {
        $this->countryCreatePage->chooseName($name);
    }

    /**
     * @When I add the :provinceName province with :prvinceCode code
     */
    public function iAddProvinceWithCode($provinceName, $provinceCode)
    {
        $this->countryCreatePage->fillProvinceNameAndCode($provinceName, $provinceCode);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->countryCreatePage->create();
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
     * @Then /^the (country "([^"]*)") should appear in the store$/
     */
    public function countryShouldAppearInTheStore(CountryInterface $country)
    {
        Assert::true(
            $this->countryIndexPage->isResourceOnPage(['code' => $country->getCode()]),
            sprintf('Country %s should exist but it does not', $country->getCode())
        );
    }

    /**
     * @Then /^(this country) should be enabled$/
     */
    public function thisCountryShouldBeEnabled(CountryInterface $country)
    {
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
        $isCountryDisabled = $this->countryIndexPage->isCountryDisabled($country);
        Assert::true(
            $isCountryDisabled,
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
     * @Then /^(this country) should have the "([^"]+)" province$/
     */
    public function countryShouldHaveProvince(CountryInterface $country, $provinceName)
    {
        expect($this->countryUpdatePage->isOpen(['id' => $country->getId()]))->toBe(true);

        expect($this->countryUpdatePage->isThereProvince($provinceName))->toBe(true);
    }
}
