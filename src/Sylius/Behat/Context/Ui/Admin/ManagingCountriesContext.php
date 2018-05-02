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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Country\CreatePageInterface;
use Sylius\Behat\Page\Admin\Country\IndexPageInterface;
use Sylius\Behat\Page\Admin\Country\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

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
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addProvince($provinceName, $provinceCode, $provinceAbbreviation);
    }

    /**
     * @When I add the postcode :postcode named :postcodeName
     */
    public function iAddPostCode($postcode, $postcodeName)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addPostCode($postcode, $postcodeName);
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
     * @Then /^(this country) should have the "([^"]*)" postcode zone/
     * @Then /^the (country "[^"]*") should have the "([^"]*)" postcode zone$/
     */
    public function countryShouldHavePostCode(CountryInterface $country, $postcodeName)
    {
        $this->iWantToEditThisCountry($country);

        Assert::true($this->updatePage->hasPostCode($postcodeName));
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
     * @Then /^(this country) should not have the "([^"]*)" postcode zone/
     * @Then /^the (country "[^"]*") should not have the "([^"]*)" postcode zone$/
     */
    public function countryShouldNotHavePostCode(CountryInterface $country, $postcodeName){
        $this->iWantToEditThisCountry($country);

        Assert::false($this->updatePage->hasPostCode($postcodeName));
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
     * @Then /^postcode with name "([^"]*)" should not be added in (this country)$/
     */
    public function postcodeWithNameShouldNotBeAdded($provinceName, CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        Assert::false($this->updatePage->hasPostCode($provinceName));
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
     * @When /^I want to create a new postcode in (country "([^"]*)")$/
     */
    public function iWantToCreateANewPostCodeInCountry(CountryInterface $country)
    {
        $this->updatePage->open(['id' => $country->getId()]);

        $this->updatePage->clickAddPostCodeButton();
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
     * @When I name the postcode :postcodeName
     * @When I do not name the postcode
     */
    public function iNameThePostCode($postcodeName = null)
    {
        $this->updatePage->namePostCode($postcodeName);
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
     * @When I do not specify the postcode value
     * @When I specify the postcode value as :postcodeValue
     */
    public function iSpecifyThePostCodeValue($postcodeValue = null)
    {
        $this->updatePage->specifyPostCodeValue($postcodeValue);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same($this->updatePage->getValidationMessage($element), sprintf('Please enter province %s.', $element));
    }

    /**
     * @Then I should be notified that :element should not be blank
     */
    public function iShouldBeNotifiedThatPostCodeValueCouldNotBeBlank($element)
    {
        Assert::same($this->updatePage->getValidationMessage($element), 'This value should not be blank for element %s.');
    }

    /**
     * @Then I should be notified that postcode should have a :element
     */
    public function iShouldBeNotifiedThatPostCodeNameShouldNotBeEmpty($element)
    {
        Assert::same($this->updatePage->getValidationMessage($element), sprintf('Please enter postcode %s.', $element));
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

    /**
     * @Then I should be notified that the postcode must be unique
     */
    public function iShouldBeNotifiedThatPostCodeMustBeUnique()
    {
        Assert::same($this->updatePage->getValidationMessage('postcode'), 'Postcode must be unique');
    }
}
