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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Component\Addressing\Model\CountryInterface;

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
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param IndexPageInterface $countryIndexPage
     * @param CreatePageInterface $countryCreatePage
     * @param UpdatePageInterface $countryUpdatePage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        IndexPageInterface $countryIndexPage,
        CreatePageInterface $countryCreatePage,
        UpdatePageInterface $countryUpdatePage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->countryIndexPage = $countryIndexPage;
        $this->countryCreatePage = $countryCreatePage;
        $this->countryUpdatePage = $countryUpdatePage;
        $this->notificationAccessor = $notificationAccessor;
    }

    /**
     * @Given I want to add a new country
     */
    public function iWantToCreateNewCountry()
    {
        $this->countryCreatePage->open();
    }

    /**
     * @When /^I choose "([^"]*)"$/
     */
    public function iChoose($name)
    {
        $this->countryCreatePage->chooseName($name);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->countryCreatePage->create();
    }

    /**
     * @Then /^I should be notified about successful creation$/
     */
    public function iShouldBeNotifiedAboutSuccessfulCreation()
    {
        expect($this->notificationAccessor->hasSuccessMessage())->toBe(true);
        expect($this->notificationAccessor->isSuccessfullyCreatedFor(self::RESOURCE_NAME))->toBe(true);
    }

    /**
     * @Then /^I should be notified about successful edition$/
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        expect($this->notificationAccessor->hasSuccessMessage())->toBe(true);
        expect($this->notificationAccessor->isSuccessfullyUpdatedFor(self::RESOURCE_NAME))->toBe(true);
    }

    /**
     * @Given /^(country "([^"]*)") should appear in the store$/
     */
    public function countryWithNameShouldAppearInTheStore(CountryInterface $country)
    {
        expect($this->countryIndexPage->isResourceOnPage(['code' => $country->getCode()]))->toBe(true);
    }

    /**
     * @Given /^I want to edit (this country)$/
     */
    public function iWantToEditThisCountry(CountryInterface $country)
    {
        $this->countryUpdatePage->open(['id' => $country->getId()]);
    }

    /**
     * @When /^I disable it$/
     */
    public function iDisableIt()
    {
        $this->countryUpdatePage->disable();
    }

    /**
     * @Given /^I save my changes$/
     */
    public function iSaveMyChanges()
    {
        $this->countryUpdatePage->saveChanges();
    }

    /**
     * @Given /^(this country) should be disabled$/
     */
    public function thisCountryShouldBeDisabled(CountryInterface $country)
    {
        expect($this->countryIndexPage->isCountryDisabled($country))->toBe(true);
    }

    /**
     * @When /^I enable it$/
     */
    public function iEnableIt()
    {
        $this->countryUpdatePage->enable();
    }

    /**
     * @Given /^(this country) should be enabled$/
     */
    public function thisCountryShouldBeEnabled(CountryInterface $country)
    {
        expect($this->countryIndexPage->isCountryEnabled($country))->toBe(true);
    }

    /**
     * @Then /^I should not be able to choose "([^"]*)"$/
     */
    public function iShouldNotBeAbleToChoose($name)
    {
        expect($this->countryCreatePage)->toThrow(ElementNotFoundException::class)->during('chooseName', [$name]);
    }

}
