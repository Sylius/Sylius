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
use Sylius\Behat\Page\Admin\Locale\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingLocalesContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     */
    public function __construct(CreatePageInterface $createPage, IndexPageInterface $indexPage)
    {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
    }

    /**
     * @Given I want to create a new locale
     * @Given I want to add a new locale
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
     * @Then the store should be available in the :name language
     */
    public function storeShouldBeAvailableInLanguage($name)
    {
        $doesLocaleExist = $this->indexPage->isSingleResourceOnPage(['name' => $name]);

        Assert::true($doesLocaleExist);
    }

    /**
     * @Then I should not be able to choose :name
     */
    public function iShouldNotBeAbleToChoose($name)
    {
        Assert::false($this->createPage->isOptionAvailable($name));
    }
}
