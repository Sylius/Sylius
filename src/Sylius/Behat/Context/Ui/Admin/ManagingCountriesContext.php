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
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingCountriesContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @When I want to add a new country
     */
    public function iWantToAddNewCountry()
    {
        $this->createPage->open();
    }

    /**
     * @When /^I want to edit (this country)$/
     */
    public function iWantToEditThisCountry(CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);
    }

    /**
     * @When I choose :countryName
     */
    public function iChoose($countryName)
    {
        $this->createPage->chooseName($countryName);
    }

    /**
     * @When I add the :provinceName province with :provinceCode code
     * @When I add the :provinceName province with :provinceCode code and :provinceAbbreviation abbreviation
     */
    public function iAddProvinceWithCode($provinceName, $provinceCode, $provinceAbbreviation = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addProvince($provinceName, $provinceCode, $provinceAbbreviation);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @When I save my changes
     * @When I try to save changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^the (country "([^"]+)") should appear in the store$/
     */
    public function countryShouldAppearInTheStore(CountryInterface $country)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $country->getCode()]));
    }

    /**
     * @Then /^(this country) should be enabled$/
     */
    public function thisCountryShouldBeEnabled(CountryInterface $country)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isCountryEnabled($country));
    }

    /**
     * @Then /^(this country) should be disabled$/
     */
    public function thisCountryShouldBeDisabled(CountryInterface $country)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isCountryDisabled($country));
    }

    /**
     * @Then I should not be able to choose :name
     */
    public function iShouldNotBeAbleToChoose($name)
    {
        try {
            $this->createPage->chooseName($name);
        } catch (ElementNotFoundException $exception) {
            return;
        }

        throw new \DomainException('Choose name should throw an exception!');
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeFieldDisabled());
    }

    /**
     * @Then /^(this country) should have the "([^"]*)" province$/
     * @Then /^the (country "[^"]*") should have the "([^"]*)" province$/
     */
    public function countryShouldHaveProvince(CountryInterface $country, $provinceName)
    {
        $this->iWantToEditThisCountry($country);

        Assert::true($this->updatePage->isThereProvince($provinceName));
    }

    /**
     * @Then /^(this country) should not have the "([^"]*)" province$/
     */
    public function thisCountryShouldNotHaveTheProvince(CountryInterface $country, $provinceName)
    {
        $this->iWantToEditThisCountry($country);

        Assert::false($this->updatePage->isThereProvince($provinceName));
    }

    /**
     * @Then /^the province should still be named "([^"]*)" in (this country)$/
     */
    public function thisProvinceShouldStillBeNamed($provinceName, CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        Assert::true($this->updatePage->isThereProvince($provinceName));
    }

    /**
     * @Then /^province with name "([^"]*)" should not be added in (this country)$/
     */
    public function provinceWithNameShouldNotBeAdded($provinceName, CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        Assert::false($this->updatePage->isThereProvince($provinceName));
    }

    /**
     * @Then /^province with code "([^"]*)" should not be added in (this country)$/
     */
    public function provinceWithCodeShouldNotBeAdded($provinceCode, CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        Assert::false($this->updatePage->isThereProvinceWithCode($provinceCode));
    }

    /**
     * @When /^I delete the "([^"]*)" province of this country$/
     */
    public function iDeleteTheProvinceOfCountry($provinceName)
    {
        $this->updatePage->removeProvince($provinceName);
    }

    /**
     * @When /^I want to create a new province in (country "([^"]*)")$/
     */
    public function iWantToCreateANewProvinceInCountry(CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        $this->updatePage->clickAddProvinceButton();
    }

    /**
     * @When I name the province :provinceName
     * @When I do not name the province
     */
    public function iNameTheProvince($provinceName = null)
    {
        $this->updatePage->nameProvince($provinceName);
    }

    /**
     * @When I do not specify the province code
     * @When I specify the province code as :provinceCode
     */
    public function iSpecifyTheProvinceCode($provinceCode = null)
    {
        $this->updatePage->specifyProvinceCode($provinceCode);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same($this->updatePage->getValidationMessage($element), sprintf('Please enter province %s.', $element));
    }

    /**
     * @When I remove :provinceName province name
     */
    public function iRemoveProvinceName($provinceName)
    {
        $this->updatePage->removeProvinceName($provinceName);
    }

    /**
     * @Then /^I should be notified that province code must be unique$/
     */
    public function iShouldBeNotifiedThatProvinceCodeMustBeUnique()
    {
        Assert::same($this->updatePage->getValidationMessage('code'), 'Province code must be unique.');
    }
}
