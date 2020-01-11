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
use Sylius\Behat\Element\Admin\TopBarElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Administrator\CreatePageInterface;
use Sylius\Behat\Page\Admin\Administrator\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingAdministratorsContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var TopBarElementInterface */
    private $topBarElement;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var RepositoryInterface */
    private $adminUserRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        TopBarElementInterface $topBarElement,
        NotificationCheckerInterface $notificationChecker,
        RepositoryInterface $adminUserRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->topBarElement = $topBarElement;
        $this->notificationChecker = $notificationChecker;
        $this->adminUserRepository = $adminUserRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I want to create a new administrator
     */
    public function iWantToCreateANewAdministrator()
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I am editing (my) details$/
     * @When /^I want to edit (this administrator)$/
     */
    public function iWantToEditThisAdministrator(AdminUserInterface $adminUser)
    {
        $this->updatePage->open(['id' => $adminUser->getId()]);
    }

    /**
     * @When I browse administrators
     * @When I want to browse administrators
     */
    public function iWantToBrowseAdministrators()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its name as :username
     * @When I do not specify its name
     */
    public function iSpecifyItsNameAs($username = null)
    {
        $this->createPage->specifyUsername($username ?? '');
    }

    /**
     * @When I change its name to :username
     */
    public function iChangeItsNameTo($username)
    {
        $this->updatePage->changeUsername($username);
    }

    /**
     * @When I specify its email as :email
     * @When I do not specify its email
     */
    public function iSpecifyItsEmailAs($email = null)
    {
        $this->createPage->specifyEmail($email ?? '');
    }

    /**
     * @When I change its email to :email
     */
    public function iChangeItsEmailTo($email)
    {
        $this->updatePage->changeEmail($email);
    }

    /**
     * @When I specify its locale as :localeCode
     */
    public function iSpecifyItsLocaleAs($localeCode)
    {
        $this->createPage->specifyLocale($localeCode);
    }

    /**
     * @When I set my locale to :localeCode
     */
    public function iSetMyLocaleTo($localeCode)
    {
        $this->updatePage->changeLocale($localeCode);
        $this->updatePage->saveChanges();
    }

    /**
     * @When I specify its password as :password
     * @When I do not specify its password
     */
    public function iSpecifyItsPasswordAs($password = null)
    {
        $this->createPage->specifyPassword($password ?? '');
    }

    /**
     * @When I change its password to :password
     */
    public function iChangeItsPasswordTo($password)
    {
        $this->updatePage->changePassword($password);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->createPage->enable();
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
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete administrator with email :email
     */
    public function iDeleteAdministratorWithEmail($email)
    {
        $this->indexPage->deleteResourceOnPage(['email' => $email]);
    }

    /**
     * @When I check (also) the :email administrator
     */
    public function iCheckTheAdministrator(string $email): void
    {
        $this->indexPage->checkResourceOnPage(['email' => $email]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When /^I (?:|upload|update) the "([^"]+)" image as (my) avatar$/
     */
    public function iUploadTheImageAsMyAvatar(string $avatar, AdminUserInterface $administrator): void
    {
        $path = $this->updateAvatar($avatar, $administrator);

        $this->sharedStorage->set($avatar, $path);
    }

    /**
     * @Then the administrator :email should appear in the store
     * @Then I should see the administrator :email in the list
     * @Then there should still be only one administrator with an email :email
     */
    public function theAdministratorShouldAppearInTheStore($email)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then this administrator with name :username should appear in the store
     * @Then there should still be only one administrator with name :username
     */
    public function thisAdministratorWithNameShouldAppearInTheStore($username)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['username' => $username]));
    }

    /**
     * @Then I should see a single administrator in the list
     * @Then /^there should be (\d+) administrators in the list$/
     */
    public function iShouldSeeAdministratorsInTheList(int $number = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $number);
    }

    /**
     * @When I remove the avatar
     */
    public function iRemoveTheAvatarImage(): void
    {
        $this->updatePage->removeAvatar();
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique()
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is already used.');
    }

    /**
     * @Then I should be notified that name must be unique
     */
    public function iShouldBeNotifiedThatNameMustBeUnique()
    {
        Assert::same($this->createPage->getValidationMessage('name'), 'This username is already used.');
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired($elementName)
    {
        Assert::same($this->createPage->getValidationMessage($elementName), sprintf('Please enter your %s.', $elementName));
    }

    /**
     * @Then I should be notified that this email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid()
    {
        Assert::same($this->createPage->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then this administrator should not be added
     */
    public function thisAdministratorShouldNotBeAdded()
    {
        $this->indexPage->open();

        Assert::same($this->indexPage->countItems(), 1);
    }

    /**
     * @Then there should not be :email administrator anymore
     */
    public function thereShouldBeNoAnymore($email)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then I should be notified that it cannot be deleted
     */
    public function iShouldBeNotifiedThatItCannotBeDeleted()
    {
        $this->notificationChecker->checkNotification(
            'Cannot remove currently logged in user.',
            NotificationType::failure()
        );
    }

    /**
     * @Then /^I should see the "([^"]*)" image as (my) avatar$/
     */
    public function iShouldSeeTheImageAsMyAvatar(string $avatar, AdminUserInterface $administrator): void
    {
        /** @var AdminUserInterface $administrator */
        $administrator = $this->adminUserRepository->findOneBy(['id' => $administrator->getId()]);

        $this->updatePage->open(['id' => $administrator->getId()]);

        Assert::same($this->sharedStorage->get($avatar), $administrator->getAvatar()->getPath());
    }

    /**
     * @Then /^I should see the "([^"]*)" avatar image in the top bar next to my name$/
     */
    public function iShouldSeeTheAvatarImageInTheTopBarNextToMyName(string $avatar): void
    {
        Assert::true($this->topBarElement->hasAvatarInMainBar($avatar));
    }

    /**
     * @Then I should not see the :avatar avatar image in the additional information section of my account
     */
    public function iShouldNotSeeTheAvatarImageInTheAdditionalInformationSectionOfMyAccount(string $avatar): void
    {
        $avatarPath = $this->sharedStorage->get($avatar);

        Assert::false($this->updatePage->hasAvatar($avatarPath));
    }

    /**
     * @Then I should not see the :avatar avatar image in the top bar next to my name
     */
    public function iShouldNotSeeTheAvatarImageInTheTopBarNextToMyName(string $avatar): void
    {
        $avatarPath = $this->sharedStorage->get($avatar);

        Assert::false($this->topBarElement->hasAvatarInMainBar($avatarPath));
        Assert::true($this->topBarElement->hasDefaultAvatarInMainBar());
    }

    private function getAdministrator(AdminUserInterface $administrator): AdminUserInterface
    {
        /** @var AdminUserInterface $administrator */
        $administrator = $this->adminUserRepository->findOneBy(['id' => $administrator->getId()]);

        return $administrator;
    }

    private function getPath(AdminUserInterface $administrator): string
    {
        $administrator = $this->getAdministrator($administrator);

        $avatar = $administrator->getAvatar();
        if (null === $avatar) {
            return '';
        }

        return $avatar->getPath() ?? '';
    }

    private function updateAvatar(string $avatar, AdminUserInterface $administrator): string
    {
        $this->updatePage->attachAvatar($avatar);
        $this->updatePage->saveChanges();

        return $this->getPath($administrator);
    }
}
