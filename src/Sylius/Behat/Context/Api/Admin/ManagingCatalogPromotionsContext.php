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
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private ApiClientInterface $client;
    private ResponseCheckerInterface $responseChecker;
    private MessageBusInterface $messageBus;
    private IriConverterInterface $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        MessageBusInterface $messageBus,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->messageBus = $messageBus;
        $this->iriConverter = $iriConverter;
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
     * @When I want to create a new catalog promotion
     */
    public function iWantToCreateANewCatalogPromotion(): void
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
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When /^I delete a ("([^"]+)" catalog promotion)$/
     */
    public function iDeletePromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->delete((string) $catalogPromotion->getCode());
    }

    /**
     * @Then there should be :amount new catalog promotion on the list
     */
    public function thereShouldBeNewCatalogPromotionOnTheList(int $amount): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->index()), $amount);
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
     * @Then this catalog promotion should be usable
     */
    public function thisCatalogPromotionShouldBeUsable(): void
    {
        Assert::isInstanceOf($this->messageBus->getDispatchedMessages()[0]['message'], CatalogPromotionUpdated::class);
    }

    /**
     * @Then the catalog promotion :catalogPromotion should be available in channel :channel
     */
    public function itShouldBeAvailableInChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel
    ): void {
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
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Catalog Promotion still exists, but it should not'
        );
    }

    /**
     * @Then /^(this catalog promotion) should no longer exist in the registry$/
     */
    public function promotionShouldNotExistInTheRegistry(CatalogPromotionInterface $catalogPromotion): void
    {
        $response = $this->client->index();
        $promotionName = (string) $catalogPromotion->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'name', $promotionName),
            sprintf('Catalog promotion with name %s still exist', $promotionName)
        );
    }
}
