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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingLocalesContext implements Context
{
    const RESOURCE_NAME = 'locale';

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
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
     * @Then I should be notified about success
     */
    public function iShouldBeNotifiedAboutSuccess()
    {
        expect($this->notificationAccessor->hasSuccessMessage())->toBe(true);
        expect($this->notificationAccessor->isSuccessfullyCreatedFor(self::RESOURCE_NAME))->toBe(true);
    }

    /**
     * @Then the store should be available in the :name language
     */
    public function storeShouldBeAvailableInLanguage($name)
    {
        expect($this->indexPage->isResourceOnPage(['name' => $name]))->toBe(true);
    }
}
