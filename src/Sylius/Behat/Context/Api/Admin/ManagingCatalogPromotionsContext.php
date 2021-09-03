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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private ApiClientInterface $client;

    private ResponseCheckerInterface $responseChecker;

    private MessageBusInterface $messageBus;

    private IriConverterInterface $iriConverter;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        MessageBusInterface $messageBus,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->messageBus = $messageBus;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I create a new catalog promotion with :code code and :name name
     */
    public function iCreateANewCatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->client->buildCreateRequest();
        $this->client->addRequestData('code', $code);
        $this->client->addRequestData('name', $name);
        $this->client->create();
    }

    /**
     * @When I create a new catalog promotion without specifying its code and name
     */
    public function iCreateANewCatalogPromotionWithoutSpecifyingItsCodeAndName(): void
    {
        $this->client->buildCreateRequest();
        $this->client->create();
    }

    /**
     * @When I want to create a new catalog promotion
     */
    public function iWantToCreateNewCatalogPromotion(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its :field as :value
     * @When I :field it :value
     */
    public function iSpecifyItsAs(string $field, string $value): void
    {
        $this->client->addRequestData($field, $value);
    }

    /**
     * @When I specify its :field as :value in :localeCode
     */
    public function iSpecifyItsAsIn(string $field, string $value, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => ['locale' => $localeCode]]];
        $data['translations'][$localeCode][$field] = $value;

        $this->client->updateRequestData($data);
    }

    /**
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => ['locale' => $localeCode]]];
        $data['translations'][$localeCode]['description'] = $description;

        $this->client->updateRequestData($data);
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->iriConverter->getIriFromItem($channel)]);
    }

    /**
     * @When /^I make (it) unavailable in (channel "[^"]+")$/
     */
    public function iMakeItUnavailableInChannel(CatalogPromotionInterface $catalogPromotion, ChannelInterface $channel): void
    {
        $channels = $this->responseChecker->getValue($this->client->show($catalogPromotion->getCode()), 'channels');

        foreach (array_keys($channels, $this->iriConverter->getIriFromItem($channel)) as $key) {
            unset($channels[$key]);
        }

        $this->client->addRequestData('channels', $channels);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I rename the :catalogPromotion catalog promotion to :name
     */
    public function iRenameTheCatalogPromotionTo(CatalogPromotionInterface $catalogPromotion, string $name): void
    {
        $this->client->buildUpdateRequest($catalogPromotion->getCode());
        $this->client->updateRequestData(['name' => $name]);
        $this->client->update();
    }

    /**
     * @When I want to modify a catalog promotion :catalogPromotion
     */
    public function iWantToModifyACatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->buildUpdateRequest($catalogPromotion->getCode());

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @When /^it gives the ("[^"]+") percentage discount$/
     */
    public function iGivesPercentageDistount(float $amount): void
    {
        $actions = [[
            'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
            'configuration' => [
                'amount' => $amount
            ],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I browse catalog promotions
     */
    public function iBrowseCatalogPromotions(): void
    {
        $this->client->index();
    }

    /**
     * @When /^it applies on variants ("[^"]+" variant) and ("[^"]+" variant)$/
     */
    public function iAddTheRuleConfiguredWithProductAnd(ProductVariantInterface $firstVariant, ProductVariantInterface $secondVariant): void
    {
        $rules = [[
            'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
            'configuration' => [
                $firstVariant->getCode(),
                $secondVariant->getCode(),
            ],
        ]];

        $this->client->addRequestData('rules', $rules);
    }

    /**
     * @When /^I want ("[^"]+" catalog promotion) to be applied on ("[^"]+" variant)$/
     */
    public function iWantCatalogPromotionToBeAppliedOn(CatalogPromotionInterface $catalogPromotion, ProductVariantInterface $productVariant): void
    {
        $this->client->buildUpdateRequest($catalogPromotion->getCode());
        $rules = [[
            'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
            'configuration' => [
                $productVariant->getCode(),
            ],
        ]];

        $this->client->updateRequestData(['rules' => $rules]);
        $this->client->update();
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to have ("[^"]+") discount$/
     */
    public function iEditCatalogPromotionToHaveDiscount(CatalogPromotionInterface $catalogPromotion, float $amount): void
    {
        $this->client->buildUpdateRequest($catalogPromotion->getCode());
        $rules = [[
            'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
            'configuration' => [
                'amount' => $amount,
            ],
        ]];

        $this->client->updateRequestData(['actions' => $rules]);
        $this->client->update();
    }

    /**
     * @When I specify nonexistent catalog promotion rule
     */
    public function ISpecifyNonexistentCatalogPromotionRule(): void
    {
        $rules = [[
            'type' => 'nonexistent_rule',
            'configuration' => [
                'config' => 'config'
            ],
        ]];

        $this->client->addRequestData('rules', $rules);
    }

    /**
     * @When I specify rule "For variants" with the wrong configuration
     */
    public function iSpecifyRuleWithWrongConfiguration(): void
    {
        $rules = [[
            'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
            'configuration' => [
                'config' => ['more_config' => 'wrong config'],
                'product' => 'some_product'
            ],
        ]];

        $this->client->addRequestData('rules', $rules);
    }

    /**
     * @Then there should be :amount new catalog promotion on the list
     * @Then there should be :amount catalog promotions on the list
     * @Then there should be an empty list of catalog promotions
     */
    public function thereShouldBeNewCatalogPromotionOnTheList(int $amount = 0): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->index()), $amount);
    }

    /**
     * @Then /^it should have ("[^"]+") discount$/
     */
    public function itShouldHaveDiscount(float $amount): void
    {
        $catalogPromotion = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        Assert::same($catalogPromotion['actions'][0]['configuration']['amount'], $amount);
    }

    /**
     * @Then /^this catalog promotion should have ("[^"]+") percentage discount$/
     */
    public function thisCatalogPromotionShouldHavePercentageDiscount(float $amount): void
    {
        $catalogPromotionActions = $this->responseChecker->getResponseContent($this->client->getLastResponse())['actions'];
        foreach ($catalogPromotionActions as $catalogPromotionAction) {
            if (
                $catalogPromotionAction['configuration']['amount'] === $amount &&
                $catalogPromotionAction['type'] === CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT
            ) {
                return;
            }
        }

        throw new \Exception(sprintf('There is no "%s" action with %f', CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT, $amount));
    }

    /**
     * @Then it should have :code code and :name name
     */
    public function itShouldHaveCodeAndName(string $code, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(), ['code' => $code, 'name' => $name]),
            sprintf('Cannot find catalog promotions with code "%s" and name "%s" in the list', $code, $name)
        );
    }

    /**
     * @Then the catalog promotions named :firstName and :secondName should be in the registry
     */
    public function theCatalogPromotionsNamedShouldBeInTheRegistry(string ...$names): void
    {
        foreach ($names as $name) {
            Assert::true(
                $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
                sprintf('Cannot find catalog promotions with name "%s" in the list', $name)
            );
        }
    }

    /**
     * @Then /^it should apply to ("[^"]+" variant) and ("[^"]+" variant)$/
     */
    public function itShouldHaveRule(ProductVariantInterface $firstVariant, ProductVariantInterface $secondVariant): void
    {
        Assert::same(
            [$firstVariant->getCode(), $secondVariant->getCode()],
            $this->responseChecker->getCollection($this->client->getLastResponse())[0]['rules'][0]['configuration']
        );
    }

    /**
     * @Then this catalog promotion should be usable
     */
    public function thisCatalogPromotionShouldBeUsable(): void
    {
        Assert::isInstanceOf($this->messageBus->getDispatchedMessages()[0]['message'], CatalogPromotionUpdated::class);
    }

    /**
     * @Then the catalog promotion :catalogPromotion should be available in channel :channel
     * @Then /^(this catalog promotion) should be available in (channel "[^"]+")$/
     */
    public function itShouldBeAvailableInChannel(CatalogPromotionInterface $catalogPromotion, ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValueInCollection(
                $this->client->show($catalogPromotion->getCode()),
                'channels',
                $this->iriConverter->getIriFromItem($channel)
            ),
            sprintf('Catalog promotion is not assigned to %s channel', $channel->getName())
        );
    }

    /**
     * @Then /^(this catalog promotion) should not be available in (channel "[^"]+")$/
     */
    public function itShouldNotBeAvailableInChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel
    ): void {
        Assert::false(
            $this->responseChecker->hasValueInCollection(
                $this->client->show($catalogPromotion->getCode()),
                'channels',
                $this->iriConverter->getIriFromItem($channel)
            ),
            sprintf('Catalog promotion is assigned to %s channel', $channel->getName())
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Catalog promotion could not be edited'
        );
    }

    /**
     * @Then /^(this catalog promotion) name should be "([^"]+)"$/
     */
    public function thisCatalogPromotionNameShouldBe(CatalogPromotionInterface $catalogPromotion, string $name): void
    {
        $response = $this->client->show($catalogPromotion->getCode());

        Assert::true(
            $this->responseChecker->hasValue($response, 'name', $name),
            sprintf('Catalog promotion\'s name %s does not exist', $name)
        );
    }

    /**
     * @Then /^(this catalog promotion) should be (labelled|described) as "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisCatalogPromotionLabelInLocaleShouldBe(
        CatalogPromotionInterface $catalogPromotion,
        string $field,
        string $value,
        string $localeCode
    ): void {
        $fieldsMapping = [
            'labelled' => 'label',
            'described' => 'description',
        ];

        $response = $this->client->show($catalogPromotion->getCode());

        Assert::true($this->responseChecker->hasTranslation($response, $localeCode, $fieldsMapping[$field], $value));
    }

    /**
     * @Then /^(this catalog promotion) should be applied on ("[^"]+" variant)$/
     */
    public function thisCatalogPromotionShouldBeAppliedOn(
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $productVariant
    ): void {
        $this->client->show($catalogPromotion->getCode());

        Assert::true($this->catalogPromotionAppliesOn($productVariant));
    }

    /**
     * @Then I should be notified that code and name are required
     */
    public function iShouldBeNotifiedThatCodeAndNameAreRequired(): void
    {
        $validationError = $this->responseChecker->getError($this->client->getLastResponse());

        Assert::contains($validationError, 'code: Please enter catalog promotion code.');
        Assert::contains($validationError, 'name: Please enter catalog promotion name.');
    }

    /**
     * @Then I should be notified that catalog promotion with this code already exists
     */
    public function iShouldBeNotifiedThatCatalogPromotionWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Catalog promotion has been created successfully, but it should not'
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The catalog promotion with given code already exists.'
        );
    }

    /**
     * @Then I should be notified that rule is invalid
     */
    public function iShouldBeNotifiedThatRuleIsInvalid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'rules: Catalog promotion rule type is invalid. Please choose a valid type'
        );
    }

    /**
     * @Then I should be notified that rule configuration is invalid
     */
    public function iShouldBeNotifiedThatRuleConfigurationIsInvalid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'rules: Provided configuration contains errors.'
        );
    }

    /**
     * @Then there should still be only one catalog promotion with code :code
     */
    public function thereShouldStillBeOnlyOneCatalogPromotionWithCode(string $code): void
    {
        Assert::count($this->responseChecker->getCollectionItemsWithValue($this->client->index(), 'code', $code), 1);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);
        $this->client->update();

        Assert::false(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'code', 'NEW_CODE'),
            'The code has been changed, but it should not'
        );
    }

    private function catalogPromotionAppliesOn(ProductVariantInterface ...$productVariants): bool
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        foreach ($productVariants as $productVariant) {
            if ($this->hasVariantInConfiguration($response['rules'][0]['configuration'], $productVariant) === false) {
                return false;
            }
        }

        return true;
    }

    private function hasVariantInConfiguration(array $configuration, ProductVariantInterface $productVariant): bool
    {
        foreach ($configuration as $productVariantIri) {
            if ($productVariantIri === $productVariant->getCode()) {
                return true;
            }
        }

        return false;
    }
}
