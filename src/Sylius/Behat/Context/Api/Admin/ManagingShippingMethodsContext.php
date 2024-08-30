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
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ManagingShippingMethodsContext implements Context
{
    use ValidationTrait;

    public const SORT_TYPES = ['ascending' => 'asc', 'descending' => 'desc'];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given I am browsing archival shipping methods
     */
    public function iAmBrowsingArchivalShippingMethods(): void
    {
        $this->client->index(Resources::SHIPPING_METHODS);
        $this->client->addFilter('exists[archivedAt]', true);
        $this->client->filter();
    }

    /**
     * @Given the shipping methods are already sorted :sortType by name
     * @When I sort the shipping methods :sortType by name
     * @When I switch the way shipping methods are sorted :sortType by name
     */
    public function iSortShippingMethodsByName(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'translation.name' => self::SORT_TYPES[$sortType],
            'localeCode' => $this->getAdminLocaleCode(),
        ]);
    }

    /**
     * @When I add the :rule rule configured with :weight
     */
    public function iAddTheRuleConfiguredWithWeight(string $rule, int $weight): void
    {
        $type = StringInflector::nameToLowercaseCode($rule);
        $this->client->addRequestData('rules', [[
            'type' => $type,
            'configuration' => [
                'weight' => $weight,
            ],
        ]]);
    }

    /**
     * @When I add the "Total weight greater than or equal" rule configured with invalid data
     */
    public function iAddTheTotalWeightGreaterThanOrEqualRuleConfiguredWithInvalidData(): void
    {
        $this->client->addRequestData('rules', [
            [
                'type' => 'total_weight_greater_than_or_equal',
                'configuration' => [
                    'weight' => true,
                ],
            ],
        ]);
    }

    /**
     * @When /^I add the "([^"]+)" rule configured with (?:€|£|\$)([^"]+) for ("[^"]+" channel)$/
     */
    public function iAddTheRuleConfiguredWithForChannel(string $rule, int $value, ChannelInterface $channel): void
    {
        match ($rule) {
            'Items total less than or equal' => $this->client->addRequestData('rules', [
                [
                    'type' => 'order_total_less_than_or_equal',
                    'configuration' => [
                        $channel->getCode() => [
                            'amount' => $value,
                        ],
                    ],
                ],
            ]),
            'Items total greater than or equal' => $this->client->addRequestData('rules', [
                [
                    'type' => 'order_total_greater_than_or_equal',
                    'configuration' => [
                        $channel->getCode() => [
                            'amount' => $value,
                        ],
                    ],
                ],
            ]),
            default => throw new \InvalidArgumentException('Unsupported shipping method rule'),
        };
    }

    /**
     * @When /^I add the "Items total less than or equal" rule configured with invalid data for ("[^"]+" channel)$/
     */
    public function iAddTheItemsTotalLessThanOrEqualRuleConfiguredWithInvalidData(ChannelInterface $channel): void
    {
        $this->client->addRequestData('rules', [
            [
                'type' => 'order_total_greater_than_or_equal',
                'configuration' => [
                    $channel->getCode() => [
                        'amount' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @When I change my locale to :localeCode
     */
    public function iSwitchTheLocaleToTheLocale(string $localeCode): void
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $this->client->buildUpdateRequest(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        $this->client->updateRequestData(['localeCode' => $localeCode]);
        $this->client->update();
    }

    /**
     * @When I am browsing shipping methods
     * @When I want to browse shipping methods
     * @When I try to browse shipping methods
     * @When I browse shipping methods
     */
    public function iBrowseShippingMethods(): void
    {
        $response = $this->client->index(Resources::SHIPPING_METHODS);

        $this->sharedStorage->set('response', $response);
    }

    /**
     * @When I (try to )delete shipping method :shippingMethod
     */
    public function iDeleteShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->delete(Resources::SHIPPING_METHODS, $shippingMethod->getCode());
    }

    /**
     * @When I want to create a new shipping method
     * @When I try to create a new shipping method
     */
    public function iWantToCreateANewShippingMethod(): void
    {
        $this->client->buildCreateRequest(Resources::SHIPPING_METHODS);
    }

    /**
     * @When I try to create a new shipping method with valid data
     */
    public function iTryToCreateANewShippingMethodWithValidData(): void
    {
        $this->client->buildCreateRequest(Resources::SHIPPING_METHODS);
        $this->client->setRequestData([
            'code' => 'FED_EX_CARRIER',
            'position' => 0,
            'translations' => ['en_US' => ['name' => 'FedEx Carrier']],
            'zone' => $this->iriConverter->getIriFromResource($this->sharedStorage->get('zone')),
            'calculator' => 'Flat rate per shipment',
            'configuration' => [$this->sharedStorage->get('channel')->getCode() => ['amount' => 50]],
        ]);
    }

    /**
     * @When I do not specify amount for :calculatorName calculator
     */
    public function iDoNotSpecifyAmountForCalculator(string $calculatorName): void
    {
        match ($calculatorName) {
            'Flat rate per shipment' => $this->client->addRequestData('calculator', DefaultCalculators::FLAT_RATE),
            'Flat rate per unit' => $this->client->addRequestData('calculator', DefaultCalculators::PER_UNIT_RATE),
            'default' => throw new \InvalidArgumentException('Unsupported calculator name'),
        };

        $channelCode = $this->sharedStorage->get('channel')->getCode();
        $this->client->addRequestData('configuration', [$channelCode => ['amount' => null]]);
    }

    /**
     * @When I remove its zone
     */
    public function iRemoveItsZone(): void
    {
        $this->client->replaceRequestData('zone', null);
    }

    /**
     * @When I try to show :shippingMethod shipping method
     */
    public function iTryToShowShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode());
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = ''): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I specify its position as :position
     */
    public function iSpecifyItsPositionAs(int $position): void
    {
        $this->client->addRequestData('position', $position);
    }

    /**
     * @When I name it :name in :localeCode
     * @When I rename it to :name in :localeCode
     * @When I do not name it
     * @When I remove its name from :localeCode translation
     */
    public function iNameItIn(?string $name = '', ?string $localeCode = 'en_US'): void
    {
        $this->client->updateRequestData(['translations' => [$localeCode => ['name' => $name]]]);
    }

    /**
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => []]];
        $data['translations'][$localeCode]['description'] = $description;

        $this->client->updateRequestData($data);
    }

    /**
     * @When /^I define it for the (zone named "[^"]+")$/
     * @When I do not specify its zone
     */
    public function iDefineItForTheZone(?ZoneInterface $zone = null): void
    {
        if (null !== $zone) {
            $this->client->addRequestData('zone', $this->iriConverter->getIriFromResource($zone));
        }
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->client->addRequestData('enabled', false);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->client->addRequestData('enabled', true);
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->iriConverter->getIriFromResource($channel)]);
    }

    /**
     * @When I choose :shippingCalculator calculator
     */
    public function iChooseCalculator(string $shippingCalculator): void
    {
        $this->client->addRequestData('calculator', $shippingCalculator);
    }

    /**
     * @When I (try to) archive the :shippingMethod shipping method
     */
    public function iArchiveTheShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->customItemAction(Resources::SHIPPING_METHODS, $shippingMethod->getCode(), HttpRequest::METHOD_PATCH, 'archive');
        $this->client->index(Resources::SHIPPING_METHODS);
    }

    /**
     * @When I (try to) restore the :shippingMethod shipping method
     */
    public function iTryToRestoreTheShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->customItemAction(Resources::SHIPPING_METHODS, $shippingMethod->getCode(), HttpRequest::METHOD_PATCH, 'restore');
    }

    /**
     * @When I specify its amount as :amount for :channel channel
     */
    public function iSpecifyItsAmountAsForChannel(ChannelInterface $channel, int $amount): void
    {
        $this->client->addRequestData('configuration', [$channel->getCode() => ['amount' => $amount]]);
    }

    /**
     * @When I want to modify a shipping method :shippingMethod
     * @When I try to modify a shipping method :shippingMethod
     * @When /^I want to modify (this shipping method)$/
     */
    public function iWantToModifyShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->client->buildUpdateRequest(Resources::SHIPPING_METHODS, $shippingMethod->getCode());
    }

    /**
     * @When I sort the shipping methods :sortType by code
     * @When I switch the way shipping methods are sorted :sortType by code
     */
    public function iSortShippingMethodsByCode(string $sortType = 'ascending'): void
    {
        $this->client->sort(['code' => self::SORT_TYPES[$sortType]]);
    }

    /**
     * @When I switch the way shipping methods are sorted by code
     */
    public function iSwitchTheWayShippingMethodsAreSortedByCode(): void
    {
        $this->client->sort(['code' => 'desc']);
    }

    /**
     * @When I switch the way shipping methods are sorted by name
     */
    public function iSwitchTheWayShippingMethodsAreSortedByName(): void
    {
        $this->client->sort(['translation.name' => 'desc']);
    }

    /**
     * @When I filter archival shipping methods
     */
    public function iFilterArchivalShippingMethods(): void
    {
        $this->client->addFilter('exists[archivedAt]', true);
        $this->client->filter();
    }

    /**
     * @Then I should see :count shipping methods in the list
     */
    public function iShouldSeeShippingMethodsInTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the shipping method :name should be in the registry
     * @Then the shipping method :name should appear in the registry
     */
    public function theShippingMethodShouldAppearInTheRegistry(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithTranslation($this->client->index(Resources::SHIPPING_METHODS), 'en_US', 'name', $name),
            sprintf('Shipping method with name %s does not exists', $name),
        );
    }

    /**
     * @Then the shipping method :name should not appear in the registry
     */
    public function theShippingMethodShouldNotAppearInTheRegistry(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithTranslation($this->client->index(Resources::SHIPPING_METHODS), 'en_US', 'name', $name),
            sprintf('Shipping method with name %s exists', $name),
        );
    }

    /**
     * @Then /^(this shipping method) should still be in the registry$/
     */
    public function thisShippingMethodShouldAppearInTheRegistry(ShippingMethodInterface $shippingMethod): void
    {
        $name = $shippingMethod->getName();

        Assert::true(
            $this->responseChecker->hasItemWithTranslation($this->client->index(Resources::SHIPPING_METHODS), 'en_US', 'name', $name),
            sprintf('Shipping method with name %s does not exists', $name),
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Shipping method could not be deleted',
        );
    }

    /**
     * @Then /^(this shipping method) should no longer exist in the registry$/
     */
    public function thisShippingMethodShouldNoLongerExistInTheRegistry(ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethodName = $shippingMethod->getName();

        Assert::false(
            $this->responseChecker->hasItemWithTranslation($this->client->index(Resources::SHIPPING_METHODS), 'en_US', 'name', $shippingMethodName),
            sprintf('Shipping method with name %s does not exists', $shippingMethodName),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Shipping method could not be created',
        );
    }

    /**
     * @Then I should be notified that my access has been denied
     */
    public function iShouldBeNotifiedThatMyAccessHasBeenDenied(): void
    {
        Assert::true($this->responseChecker->hasAccessDenied($this->client->getLastResponse()));
    }

    /**
     * @Then the shipping method :shippingMethod should be available in channel :channel
     */
    public function theShippingMethodShouldBeAvailableInChannel(ShippingMethodInterface $shippingMethod, ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValueInCollection(
                $this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode()),
                'channels',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin'),
            ),
            sprintf('Shipping method is not assigned to %s channel', $channel->getName()),
        );
    }

    /**
     * @Then /^(this shipping method) name should be "([^"]+)"$/
     * @Then /^(this shipping method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(ShippingMethodInterface $shippingMethod, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasTranslation(
                $this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode()),
                'en_US',
                'name',
                $name,
            ),
            'Shipping method name has not been changed',
        );
    }

    /**
     * @Then /^(this shipping method) should be disabled$/
     */
    public function thisShippingMethodShouldBeDisabled(ShippingMethodInterface $shippingMethod): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode()),
                'enabled',
                false,
            ),
            'Shipping method name is not disabled',
        );
    }

    /**
     * @Then /^(this shipping method) should be enabled$/
     */
    public function thisShippingMethodShouldBeEnabled(ShippingMethodInterface $shippingMethod): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode()),
                'enabled',
                true,
            ),
            'Shipping method name is not disabled',
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exist',
        );
    }

    /**
     * @Then I should be notified that shipping method with this code already exists
     */
    public function iShouldBeNotifiedThatShippingMethodWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Shipping method  has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The shipping method with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one shipping method with code :value
     */
    public function thereShouldStillBeOnlyOneShippingMethodWith(string $value): void
    {
        $response = $this->client->index(Resources::SHIPPING_METHODS);
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, 1, sprintf('Expected 1 shipping method, but got %d', $itemsCount));
        Assert::true($this->responseChecker->hasItemWithValue($response, 'code', $value));
    }

    /**
     * @Then the only shipping method on the list should be :name
     */
    public function theOnlyShippingMethodOnTheListShouldBe(string $name): void
    {
        $response = $this->client->getLastResponse();
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, 1, sprintf('Expected 1 shipping method, but got %d', $itemsCount));
        Assert::true($this->responseChecker->hasItemWithTranslation($response, 'en_US', 'name', $name));
    }

    /**
     * @Then I should see :amount shipping methods on the list
     */
    public function iShouldSeeShippingMethodOnTheList(int $amount): void
    {
        $this->client->index(Resources::SHIPPING_METHODS);

        $response = $this->client->getLastResponse();
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, $amount, sprintf('Expected 1 shipping method, but got %d', $itemsCount));
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the shipping method is in use.',
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter shipping method %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that zone has to be selected
     */
    public function iShouldBeNotifiedThatZoneHasToBeSelected(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'zone: Please select shipping method zone.',
        );
    }

    /**
     * @Then I should be notified that the zone is required
     */
    public function iShouldBeNotifiedThatZoneHasToBeIriAndCannotBeNull(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Expected IRI or nested document for attribute "zone", "NULL" given.',
        );
    }

    /**
     * @Then shipping method with :element :value should not be added
     */
    public function theShippingMethodWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::SHIPPING_METHODS), $element, $value),
            sprintf('Shipping method should not have %s "%s", but it does,', $element, $value),
        );
    }

    /**
     * @Then the first shipping method on the list should have code :value
     */
    public function theFirstProductOnTheListShouldHave(string $value): void
    {
        $shippingMethods = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(reset($shippingMethods)['code'], $value);
    }

    /**
     * @Then the first shipping method on the list should have name :value
     */
    public function theFirstShippingMethodOnTheListShouldHave(string $value): void
    {
        $shippingMethods = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(reset($shippingMethods)['translations'][$this->getAdminLocaleCode()]['name'], $value);
    }

    /**
     * @Then the last shipping method on the list should have name :value
     */
    public function theLastShippingMethodOnTheListShouldHave(string $value): void
    {
        $response = $this->sharedStorage->has('response') ? $this->sharedStorage->get('response') : $this->client->getLastResponse();

        $shippingMethods = $this->responseChecker->getCollection($response);

        Assert::same(end($shippingMethods)['translations']['en_US']['name'], $value);
    }

    /**
     * @Then I should be viewing non archival shipping methods
     */
    public function iShouldBeViewingNonArchivalShippingMethods(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then shipping method :shippingMethod should still have code :code
     */
    public function shippingMethodShouldStillHaveCode(ShippingMethodInterface $shippingMethod, string $code): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->show(Resources::SHIPPING_METHODS, $shippingMethod->getCode()), 'code'),
            $code,
        );
    }

    /**
     * @Then I should be notified that amount for :channel channel should not be blank
     */
    public function iShouldBeNotifiedThatAmountForChannelShouldNotBeBlank(ChannelInterface $channel): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('configuration[%s][amount]: This value should not be blank.', $channel->getCode()),
        );
    }

    /**
     * @Then I should be notified that code needs to contain only specific symbols
     */
    public function iShouldBeNotifiedThatCodeNeedsToContainOnlySpecificSymbols(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'code: Shipping method code can only be comprised of letters, numbers, dashes and underscores.',
        );
    }

    /**
     * @Then I should be notified that shipping charge for :channel channel cannot be lower than 0
     */
    public function iShouldBeNotifiedThatShippingChargeForChannelCannotBeLowerThan0(ChannelInterface $channel): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('configuration[%s][amount]: Shipping charge cannot be lower than 0.', $channel->getCode()),
        );
    }

    /**
     * @Then I should be notified that the weight rule has an invalid configuration
     */
    public function iShouldBeNotifiedThatTheWeightRuleHasAnInvalidConfiguration(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'configuration[weight]: This value should be of type numeric.',
        );
    }

    /**
     * @Then I should be notified that the amount rule has an invalid configuration in :channel channel
     */
    public function iShouldBeNotifiedThatTheAmountRuleHasAnInvalidConfigurationInChannel(ChannelInterface $channel): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('configuration[%s][amount]: This value should be of type numeric.', $channel->getCode()),
        );
    }

    private function getAdminLocaleCode(): string
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $response = $this->client->show(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        return $this->responseChecker->getValue($response, 'localeCode');
    }
}
