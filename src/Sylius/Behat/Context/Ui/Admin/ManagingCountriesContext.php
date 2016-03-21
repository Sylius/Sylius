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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingCountriesContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $countryIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $countryCreatePage;

    /**
     * @param IndexPageInterface $countryIndexPage
     * @param CreatePageInterface $countryCreatePage
     */
    public function __construct(
        IndexPageInterface $countryIndexPage,
        CreatePageInterface $countryCreatePage
    ) {
        $this->countryIndexPage = $countryIndexPage;
        $this->countryCreatePage = $countryCreatePage;
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
     * @Then I should be notified about success
     */
    public function iShouldBeNotifiedAboutSuccess()
    {
        expect($this->countryIndexPage->hasSuccessMessage())->toBe(true);
        expect($this->countryIndexPage->isSuccessfullyCreated())->toBe(true);
    }

    /**
     * @Given /^(country "([^"]*)") should appear in the store$/
     */
    public function countryWithNameShouldAppearInTheStore(CountryInterface $country)
    {
        expect($this->countryIndexPage->isResourceOnPage(['code' => $country->getCode()]))->toBe(true);
    }

    /**
     * @Then /^I should not be able to choose "([^"]*)"$/
     */
    public function iShouldNotBeAbleToChoose($name)
    {
        expect($this->countryCreatePage)->toThrow(ElementNotFoundException::class)->during('chooseName', [$name]);
    }

}
