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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Webmozart\Assert\Assert;

final class ManagingZonesContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
        private string $zoneMemberClass,
    ) {
    }

    /**
     * @When I want to create a new zone consisting of :memberType
     */
    public function iWantToCreateANewZoneConsistingOfCountry(string $memberType): void
    {
        $this->client->buildCreateRequest(Resources::ZONES);
        $this->client->addRequestData('type', $memberType);
    }

    /**
     * @When I name it :name
     * @When I rename it to :name
     */
    public function iNameIt(string $name): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I do not specify its :type
     * @When I do not add a country member
     */
    public function iDoNotSpecifyItsField(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I add a country :country
     */
    public function iAddACountry(CountryInterface $country): void
    {
        $this->client->addSubResourceData('members', [
            'code' => $country->getCode(),
        ]);
    }

    /**
     * @When I add a province :province
     */
    public function iAddAProvince(ProvinceInterface $province): void
    {
        $this->client->addSubResourceData('members', [
            'code' => $province->getCode(),
        ]);
    }

    /**
     * @When I add a zone :zone
     */
    public function iAddAZone(ZoneInterface $zone): void
    {
        $this->client->addSubResourceData('members', [
            'code' => $zone->getCode(),
        ]);
    }

    /**
     * @When I provide a too long zone member code
     */
    public function iProvideATooLongZoneMemberCode(): void
    {
        $this->client->addSubResourceData('members', [
            'code' => str_repeat('a', $this->getMaxCodeLength() + 1),
        ]);
    }

    /**
     * @When I select its scope as :scope
     */
    public function iSelectItsScopeAs(string $scope): void
    {
        $this->client->addRequestData('scope', $scope);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I want to see all zones in store
     * @When I browse zones
     */
    public function iWantToSeeAllZonesInStore(): void
    {
        $this->client->index(Resources::ZONES);
    }

    /**
     * @When /^I(?:| try to) delete the (zone named "([^"]*)")$/
     */
    public function iDeleteZoneNamed(ZoneInterface $zone): void
    {
        $this->client->delete(Resources::ZONES, $zone->getCode());
    }

    /**
     * @When I want to modify the zone named :zone
     */
    public function iWantToModifyTheZoneNamed(ZoneInterface $zone): void
    {
        $this->sharedStorage->set('zone', $zone);

        $this->client->buildUpdateRequest(Resources::ZONES, $zone->getCode());
    }

    /**
     * @When /^I(?:| also) remove the ("([^"]+)" country) member$/
     */
    public function iRemoveTheCountryMember(CountryInterface $country): void
    {
        $this->removeZoneMember($country);
    }

    /**
     * @When /^I(?:| also) remove the ("([^"]+)", "([^"]+)" and "([^"]+)" country) members$/
     */
    public function iRemoveCountryMembers(array $countries): void
    {
        foreach ($countries as $country) {
            $this->removeZoneMember($country);
        }
    }

    /**
     * @When I remove the :province province member
     */
    public function iRemoveTheProvinceMember(ProvinceInterface $province): void
    {
        $this->removeZoneMember($province);
    }

    /**
     * @When I remove the :zone zone member
     */
    public function iRemoveTheZoneMember(ZoneInterface $zone): void
    {
        $this->removeZoneMember($zone);
    }

    /**
     * @When I add the country :country again
     */
    public function iAddTheCountryToTheZoneNamedAgain(CountryInterface $country): void
    {
        $this->iWantToModifyTheZoneNamed($this->sharedStorage->get('zone'));
        $this->client->addSubResourceData('members', [
            'code' => $country->getCode(),
        ]);

        $this->client->update();
    }

    /**
     * @Then the zone named :zone with the :country country member should appear in the registry
     */
    public function theZoneNamedWithTheCountryMemberShouldAppearInTheRegistry(
        ZoneInterface $zone,
        CountryInterface $country,
    ): void {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::ZONES, 'members', $zone->getCode()),
            'code',
            $country->getCode(),
        ));
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exists',
        );
    }

    /**
     * @Then I can not add a zone :zone
     */
    public function iCanNotAddAZone(ZoneInterface $zone): void
    {
        $this->client->addSubResourceData('members', [
            'code' => $zone->getCode(),
        ]);

        Assert::contains(
            $this->responseChecker->getError($this->client->update()),
            'members: Zone member cannot be the same as a zone.',
        );
    }

    /**
     * @Then the zone named :zone with the :province province member should appear in the registry
     */
    public function theZoneNamedWithTheProvinceMemberShouldAppearInTheRegistry(
        ZoneInterface $zone,
        ProvinceInterface $province,
    ): void {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::ZONES, 'members', $zone->getCode()),
            'code',
            $province->getCode(),
        ));
    }

    /**
     * @Then the zone named :zone with the :otherZone zone member should appear in the registry
     */
    public function theZoneNamedWithTheZoneMemberShouldAppearInTheRegistry(
        ZoneInterface $zone,
        ZoneInterface $otherZone,
    ): void {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::ZONES, 'members', $zone->getCode()),
            'code',
            $otherZone->getCode(),
        ));
    }

    /**
     * @Then its scope should be :scope
     */
    public function itsScopeShouldBe(string $scope): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::ZONES, 'EU'), 'scope', $scope),
            sprintf('Its Zone does not have %s scope', $scope),
        );
    }

    /**
     * @Then I should see :count zones in the list
     * @Then I should see a single zone in the list
     */
    public function iShouldSeeZonesInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::ZONES)), $count);
    }

    /**
     * @Then I should see the zone named :name in the list
     * @Then I should still see the zone named :name in the list
     */
    public function iShouldSeeTheZoneNamedInTheList(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::ZONES), 'name', $name),
            sprintf('There is no zone with name "%s"', $name),
        );
    }

    /**
     * @Then there should still be only one zone with code :code
     */
    public function thereShouldStillBeOnlyOneZoneWithCode(string $code): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::ZONES), 'code', $code),
            1,
            sprintf('There should be only one zone with code "%s"', $code),
        );
    }

    /**
     * @Then the zone named :name should no longer exist in the registry
     */
    public function theZoneNamedShouldNoLongerExistInTheRegistry(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::ZONES), 'name', $name),
            sprintf('Zone with name %s exists', $name),
        );
    }

    /**
     * @Then /^zone with (code|name) "([^"]*)" should not be added$/
     */
    public function zoneShouldNotBeAdded(string $field, string $value): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::ZONES), $field, $value),
            sprintf('Zone with %s %s exists', $field, $value),
        );
    }

    /**
     * @Then /^(this zone) should have only (the "([^"]*)" (?:country|province|zone) member)$/
     */
    public function thisZoneShouldHaveOnlyTheProvinceMember(ZoneInterface $zone, ZoneMemberInterface $zoneMember): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::ZONES, 'members', $zone->getCode()),
            'code',
            $zoneMember->getCode(),
        ));

        Assert::same(
            $this->responseChecker->countCollectionItems($this->client->getLastResponse()),
            1,
        );
    }

    /**
     * @Then /^(this zone) should have ("([^"]+)" and "([^"]+)" country members)$/
     */
    public function thisZoneShouldHaveTheCountryAndTheProvinceMembers(
        ZoneInterface $zone,
        array $zoneMembers,
    ): void {
        $response = $this->client->subResourceIndex(Resources::ZONES, 'members', $zone->getCode());

        foreach ($zoneMembers as $zoneMember) {
            Assert::true($this->responseChecker->hasItemWithValue(
                $response,
                'code',
                $zoneMember->getCode(),
            ));
        }

        Assert::same(
            $this->responseChecker->countCollectionItems($response),
            2,
        );
    }

    /**
     * @Then /^(this zone) name should be "([^"]*)"$/
     */
    public function thisZoneNameShouldBe(ZoneInterface $zone, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::ZONES, $zone->getCode()), 'name', $name),
            sprintf('Its Zone does not have name %s.', $name),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Zone could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful(
                $this->client->getLastResponse(),
            ),
            'Zone could not be deleted',
        );
    }

    /**
     * @Then I should be notified that this zone cannot be deleted
     * @Then I should be notified that the zone is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatThisZoneCannotBeDeleted(): void
    {
        Assert::false(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Zone can be deleted, but it should not',
        );
    }

    /**
     * @Then I should be notified that zone with this code already exists
     */
    public function iShouldBeNotifiedThatZoneWithThisCodeAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'code: Zone code must be unique.',
        );
    }

    /**
     * @Then /^I should be notified that (code|name) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter zone %s.', $element),
        );
    }

    /**
     * @Then I should be notified that at least one zone member is required
     */
    public function iShouldBeNotifiedThatAtLeastOneZoneMemberIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'members: Please add at least 1 zone member.',
        );
    }

    /**
     * @Then I should be informed that the provided zone member code is too long
     */
    public function iShouldBeNotifiedThatTheZoneMemberCodeIsTooLong(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The zone member code must not be longer than',
        );
    }

    private function removeZoneMember(CountryInterface|ProvinceInterface|ZoneInterface $objectToRemove): void
    {
        /** @var ZoneInterface $zone */
        $zone = $this->sharedStorage->get('zone');

        $members = $zone->getMembers();

        foreach ($members as $member) {
            if ($member->getCode() === $objectToRemove->getCode()) {
                $objectToRemove = $member;
            }
        }

        $iri = $this->iriConverter->getItemIriFromResourceClass($this->zoneMemberClass, ['id' => $objectToRemove->getId()]);

        $this->client->removeSubResource('members', $iri);
    }
}
