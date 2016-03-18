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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Locale\CreatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingLocalesContext implements Context
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
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     */
    public function __construct(IndexPageInterface $indexPage, CreatePageInterface $createPage)
    {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
    }

    /**
     * @Given I want to create a new locale
     */
    public function iWantToCreateNewLocale()
    {
        $this->createPage->open();
    }

    /**
     * @When I choose :name
     */
    public function iChoose($name)
    {
        $this->createPage->chooseName($name);
    }

    /**
     * @When I add it
     */
    public function iAdd()
    {
        $this->createPage->create();
    }

    /**
     * @Then I should be notified about success
     */
    public function iShouldBeNotifiedAboutSuccess()
    {
        expect($this->indexPage->hasSuccessMessage())->toBe(true);
        expect($this->indexPage->isSuccessfullyCreated())->toBe(true);
    }

    /**
     * @Then the store should be available in the :name language
     */
    public function storeShouldBeAvailableInLanguage($name)
    {
        expect($this->indexPage->isResourceOnPage(['name' => $name]))->toBe(true);
    }
}
