<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CountryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $adminCountryIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $adminCountryCreatePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $adminCountryIndexPage
     * @param CreatePageInterface $adminCountryCreatePage
     */
    public function __construct(SharedStorageInterface $sharedStorage, IndexPageInterface $adminCountryIndexPage, CreatePageInterface $adminCountryCreatePage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->adminCountryIndexPage = $adminCountryIndexPage;
        $this->adminCountryCreatePage = $adminCountryCreatePage;
    }

    /**
     * @Given I want to create new country
     */
    public function iWantToCreateNewCountry()
    {
        $this->adminCountryCreatePage->open();
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        $this->sharedStorage->set('countryName', $name);
        $this->adminCountryCreatePage->fillName($name);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->adminCountryCreatePage->create();
    }

    /**
     * @Then I should be notified about success
     */
    public function iShouldBeNotifiedAboutSuccess()
    {
        expect($this->adminCountryIndexPage->isSuccessfulMessage())->toBe(true);
        expect($this->adminCountryIndexPage->isSuccessfullyCreated())->toBe(true);
    }

    /**
     * @Then this country should appear in the store
     */
    public function thisCountryShouldAppearInTheStore()
    {
        expect($this->adminCountryIndexPage->isResourceAppearInTheStoreBy(['name' => $this->sharedStorage->get('countryName')]))->toBe(true);
    }
}
