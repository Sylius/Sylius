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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
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
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
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
}
