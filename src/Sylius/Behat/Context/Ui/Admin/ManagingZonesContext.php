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
use Sylius\Behat\Element\Admin\Zone\FormElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Webmozart\Assert\Assert;

final class ManagingZonesContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private FormElementInterface $formElement,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I want to create a new zone consisting of :memberType
     */
    public function iWantToCreateANewZoneWithMembers(string $memberType): void
    {
        $this->createPage->open(['type' => $memberType]);
    }

    /**
     * @When I browse zones
     * @When I want to see all zones in store
     */
    public function iWantToSeeAllZonesInStore(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I want to modify the (zone named "[^"]+")$/
     */
    public function iWantToModifyAZoneNamed(ZoneInterface $zone): void
    {
        $this->updatePage->open(['id' => $zone->getId()]);
    }

    /**
     * @When /^I(?:| try to) delete the (zone named "([^"]+)")$/
     */
    public function iDeleteZoneNamed(ZoneInterface $zone): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $zone->getName(), 'code' => $zone->getCode()]);
    }

    /**
     * @When /^I(?:| also) remove the "([^"]+)" (?:country|province|zone) member$/
     * @When /^I(?:| also) remove the "([^"]+)", "([^"]+)" and "([^"]+)" (?:country|province|zone) members$/
     */
    public function iRemoveMembers(string ...$members): void
    {
        foreach ($members as $member) {
            $this->formElement->removeMember($member);
        }
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     * @When I do not specify its name
     */
    public function iNameIt(string $name = ''): void
    {
        $this->formElement->nameIt($name);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(string $code = ''): void
    {
        $this->formElement->specifyCode($code);
    }

    /**
     * @When I specify a too long code
     */
    public function iSpecifyATooLong(): void
    {
        $this->formElement->specifyCode(str_repeat('a', 256));
    }

    /**
     * @When I do not add a country member
     */
    public function iDoNotAddACountryMember(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When /^I add a (?:country|province|zone) "([^"]+)"$/
     */
    public function iAddAZoneMember(string $name): void
    {
        $this->formElement->addMember();
        $this->formElement->chooseMember($name);
    }

    /**
     * @When I select its scope as :scope
     */
    public function iSelectItsScopeAs($scope): void
    {
        $this->formElement->selectScope($scope);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I check (also) the :zoneName zone
     */
    public function iCheckTheZone(string $zoneName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $zoneName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then /^the (zone named "[^"]+") with the "([^"]+)" (?:country|province|zone) member should appear in the registry$/
     */
    public function theZoneWithTheCountryShouldAppearInTheRegistry(
        ZoneInterface $zone,
        string $member,
    ): void {
        $this->updatePage->open(['id' => $zone->getId()]);

        Assert::same($this->formElement->getName(), $zone->getName());
        Assert::true(
            $this->formElement->hasMember($member),
            sprintf('Zone %s has no member %s', $zone->getName(), $member),
        );
    }

    /**
     * @Given its scope should be :scope
     */
    public function itsScopeShouldBe(string $scope): void
    {
        Assert::same($this->formElement->getScope(), $scope);
    }

    /**
     * @Then /^(this zone) should have only the "([^"]*)" (?:country|province|zone) member$/
     */
    public function thisZoneShouldHaveOnlyTheMember(
        ZoneInterface $zone,
        string $member,
    ): void {
        $this->updatePage->open(['id' => $zone->getId()]);

        Assert::same($this->formElement->countMembers(), 1);
        Assert::true(
            $this->formElement->hasMember($member),
            sprintf('Zone %s has no member %s', $zone->getName(), $member),
        );
    }

    /**
     * @Then /^(this zone) name should be "([^"]*)"/
     */
    public function thisZoneNameShouldBe(ZoneInterface $zone, string $name): void
    {
        $this->updatePage->open(['id' => $zone->getId()]);

        Assert::same($this->formElement->getName(), $name);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @Then /^I should be notified that zone with this code already exists$/
     */
    public function iShouldBeNotifiedThatZoneWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'Zone code must be unique.');
    }

    /**
     * @Then /^there should still be only one zone with code "([^"]*)"$/
     */
    public function thereShouldStillBeOnlyOneZoneWithCode(string $code): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::same($this->formElement->getValidationMessage($element), sprintf('Please enter zone %s.', $element));
    }

    /**
     * @Then zone with :element :value should not be added
     */
    public function zoneWithNameShouldNotBeAdded(string $element, string $value): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^I should be notified that at least one zone member is required$/
     */
    public function iShouldBeNotifiedThatAtLeastOneZoneMemberIsRequired(): void
    {
        Assert::same($this->formElement->getFormValidationMessage(), 'Please add at least 1 zone member.');
    }

    /**
     * @Then I should not be able to edit its type
     */
    public function iShouldNotBeAbleToEditItsType(): void
    {
        Assert::true($this->formElement->isTypeFieldDisabled());
    }

    /**
     * @Then it should be of :type type
     */
    public function itShouldBeOfType(string $type): void
    {
        Assert::same(
            strtolower($this->formElement->getType()),
            strtolower($type),
        );
    }

    /**
     * @Then the zone named :zoneName should no longer exist in the registry
     */
    public function thisZoneShouldNoLongerExistInTheRegistry(string $zoneName): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $zoneName]));
    }

    /**
     * @Then I should see a single zone in the list
     * @Then I should see :amount zones in the list
     */
    public function iShouldSeeZonesInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then /^I should(?:| still) see the (zone named "([^"]+)") in the list$/
     */
    public function iShouldSeeTheZoneNamedInTheList(ZoneInterface $zone): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $zone->getCode(), 'name' => $zone->getName()]));
    }

    /**
     * @Then I should be notified that the zone is in use and cannot be deleted
     * @Then I should be notified that this zone cannot be deleted
     */
    public function iShouldBeNotifiedThatTheZoneIsInUseAndCannotBeDeleted(): void
    {
        $this->notificationChecker->checkNotification('Error Cannot delete, the Zone is in use.', NotificationType::failure());
    }

    /**
     * @Then I should be notified that code is too long
     */
    public function iShouldBeNotifiedThatCodeIsTooLong(): void
    {
        Assert::contains(
            $this->formElement->getValidationMessage('code'),
            'must not be longer than 255 characters.',
        );
    }

    /**
     * @Then /^I should not be able to add the "([^"]+)" (?:country|province|zone) as a member$/
     */
    public function iShouldNotBeAbleToAddTheMember(string $name): void
    {
        $this->formElement->addMember();

        try {
            $this->formElement->chooseMember($name);
        } catch (ElementNotFoundException) {
            return;
        }

        throw new \InvalidArgumentException(sprintf('Member "%s" should not be selectable.', $name));
    }
}
