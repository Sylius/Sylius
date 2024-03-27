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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Country\CreatePageInterface;
use Sylius\Behat\Page\Admin\Country\IndexPageInterface;
use Sylius\Behat\Page\Admin\Country\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

final class ManagingCountriesContext implements Context
{
    private const MAX_PROVINCE_CODE_LENGTH = 255;

    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
    ) {
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
     * @When /^I am editing (this country)$/
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
     * @When I add it
     * @When I try to add it
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
     * @When I try to save my changes
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
        } catch (ElementNotFoundException) {
            return;
        }

        throw new \DomainException('Choose name should throw an exception!');
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeFieldDisabled());
    }

    /**
     * @Then /^(this country) should(?:| still) have the "([^"]*)" province$/
     * @Then /^(this country) should(?:| still) have the "([^"]*)" and "([^"]*)" provinces$/
     * @Then /^the (country "[^"]*") should(?:| still) have the "([^"]*)" province$/
     */
    public function countryShouldHaveProvince(CountryInterface $country, string ...$provinceNames)
    {
        $this->iWantToEditThisCountry($country);

        foreach ($provinceNames as $provinceName) {
            Assert::true($this->updatePage->isThereProvince($provinceName));
        }
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
     * @When /^I(?:| also) delete the "([^"]*)" province of this country$/
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
        $this->updatePage->nameProvince($provinceName ?? '');
    }

    /**
     * @When I do not specify the province code
     * @When I specify the province code as :provinceCode
     */
    public function iSpecifyTheProvinceCode($provinceCode = null)
    {
        $this->updatePage->specifyProvinceCode($provinceCode ?? '');
    }

    /**
     * @When I provide a too long province code
     */
    public function iProvideTooLongProvinceCode(): void
    {
        $this->iSpecifyTheProvinceCode(sprintf('US-%s', str_repeat('A', self::MAX_PROVINCE_CODE_LENGTH)));
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
    public function iRemoveProvinceName(string $provinceName): void
    {
        $this->updatePage->removeProvinceName($provinceName);
        $this->iSaveMyChanges();
    }

    /**
     * @Then /^I should be notified that province (code|name) must be unique$/
     */
    public function iShouldBeNotifiedThatProvinceCodeMustBeUnique(string $field): void
    {
        Assert::same($this->updatePage->getValidationMessage($field), sprintf('Province %s must be unique.', $field));
    }

    /**
     * @Then I should be notified that all province codes and names within this country need to be unique
     */
    public function iShouldBeNotifiedThatAllProvinceCodesAndNamesWithinThisCountryNeedToBeUnique(): void
    {
        Assert::inArray(
            'All provinces within this country need to have unique codes and names.',
            $this->updatePage->getFormValidationErrors(),
        );
    }

    /**
     * @Then I should be notified that name of the province is required
     */
    public function iShouldBeNotifiedThatNameOfTheProvinceIsRequired(): void
    {
        Assert::same($this->updatePage->getValidationMessage('name'), 'Please enter province name.');
    }

    /**
     * @Then I should be informed that the provided province code is too long
     */
    public function iShouldBeInformedThatTheCodeIsTooLong(): void
    {
        Assert::contains($this->updatePage->getValidationMessage('code'), 'The code must not be longer than');
    }

    /**
     * @Then I should be notified that provinces that are in use cannot be deleted
     */
    public function iShouldBeNotifiedThatProvincesThatAreInUseCannotBeDeleted(): void
    {
        $this->notificationChecker->checkNotification('Error Cannot delete, the province is in use.', NotificationType::failure());
    }
}
