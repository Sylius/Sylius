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
use Sylius\Behat\Page\Admin\Locale\IndexPageInterface;
use Sylius\Behat\Page\Admin\Locale\UpdatePageInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingLocalesContext implements Context
{
    const RESOURCE_NAME = 'locale';

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->notificationAccessor = $notificationAccessor;
    }

    /**
     * @Given I want to create a new locale
     */
    public function iWantToCreateNewLocale()
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I want to edit (this locale)$/
     */
    public function iWantToEditThisLocale(LocaleInterface $locale)
    {
        $this->updatePage->open(['id' => $locale->getId()]);
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
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified about successful creation
     */
    public function iShouldBeNotifiedAboutSuccessfulCreation()
    {
        $doesSuccessMessageAppear = $this->notificationAccessor->hasSuccessMessage();
        Assert::true(
            $doesSuccessMessageAppear,
            sprintf('Message type is not positive')
        );

        $doesSuccessfulCreationMessageAppear = $this->notificationAccessor->isSuccessfullyCreatedFor(self::RESOURCE_NAME);
        Assert::true(
            $doesSuccessfulCreationMessageAppear,
            sprintf('Successful creation message does not appear')
        );
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(),
            'Message type is not positive'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyUpdatedFor(self::RESOURCE_NAME),
            'Successful edition message does not appear'
        );
    }

    /**
     * @Then the store should be available in the :name language
     */
    public function storeShouldBeAvailableInLanguage($name)
    {
        $doesLocaleExist = $this->indexPage->isResourceOnPage(['name' => $name]);
        Assert::true(
            $doesLocaleExist,
            sprintf('Locale %s should exist but it does not', $name)
        );
    }

    /**
     * @Then /^(this locale) should be enabled$/
     */
    public function thisLocaleShouldBeEnabled(LocaleInterface $locale)
    {
        Assert::true(
            $this->indexPage->isLocaleEnabled($locale),
            sprintf('Locale %s should be enabled but it is not', $locale->getCode())
        );
    }

    /**
     * @Then /^(this locale) should be disabled$/
     */
    public function thisLocaleShouldBeDisabled(LocaleInterface $locale)
    {
        Assert::true(
            $this->indexPage->isLocaleDisabled($locale),
            sprintf('Locale %s should be disabled but it is not', $locale->getCode())
        );
    }
}
