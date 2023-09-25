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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I want to browse promotions
     * @When I browse promotions
     */
    public function iWantToBrowsePromotions(): void
    {
        $this->client->index(Resources::PROMOTIONS);
    }

    /**
     * @When I want to create a new promotion
     */
    public function iWantToCreateANewPromotion(): void
    {
        $this->client->buildCreateRequest(Resources::PROMOTIONS);
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
     * @When I set it as not applies to discounted by catalog promotion items
     */
    public function iSetItAsNotAppliesToDiscountedByCatalogPromotionItems(): void
    {
        $this->client->updateRequestData(['appliesToDiscounted' => false]);
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
     */
    public function thereShouldBePromotion(int $amount = 1): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->client->getLastResponse())),
            $amount,
        );
    }

    /**
     * @Then the :promotionName promotion should exist in the registry
     */
    public function thePromotionShouldAppearInTheRegistry(string $promotionName): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotionName,
        ));

        Assert::notNull($returnedPromotion, sprintf('There is no promotion %s in registry', $promotionName));
    }

    /**
     * @Then the :promotionName promotion shouldn't be listed on the current page
     */
    public function thePromotionShouldntBeListedOnTheCurrentPage(string $promotionName): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotionName,
        ));

        Assert::false($returnedPromotion, sprintf('There is promotion %s in registry', $promotionName));
    }

    /**
     * @Then the :promotionName promotion should be listed on the current page
     */
    public function thePromotionShouldBeListedOnTheCurrentPage(string $promotionName): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotionName,
        ));

        Assert::isArray($returnedPromotion, sprintf('There is no promotion %s in registry', $promotionName));
    }

    /**
     * @Then I should see :amount promotions on the list
     */

    public function iShouldSeePromotionOnTheList(int $amount): void
    {
        $this->client->index(Resources::PROMOTIONS);

        $response = $this->client->getLastResponse();
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, $amount, sprintf('Expected %d promotion, but got %d', $amount, $itemsCount));
    }

    /**
     * @Then /^(this promotion) should be coupon based$/
     */
    public function thisPromotionShouldBeCouponBased(PromotionInterface $promotion): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotion->getName(),
        ));

        Assert::true(
            $returnedPromotion['couponBased'],
            sprintf('The promotion %s isn\'t coupon based', $promotion->getName()),
        );
    }

    /**
     * @Then /^I should be able to manage coupons for (this promotion)$/
     */
    public function iShouldBeAbleToManageCouponsForThisPromotion(PromotionInterface $promotion): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotion->getName(),
        ));

        Assert::keyExists($returnedPromotion, 'coupons');
    }

    /**
     * @When /^I delete a ("([^"]+)" promotion)$/
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iDeletePromotion(PromotionInterface $promotion): void
    {
        $this->client->delete(Resources::PROMOTIONS, $promotion->getCode());
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Promotion still exists, but it should not',
        );
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion): void
    {
        $response = $this->client->index(Resources::PROMOTIONS);
        $promotionName = (string) $promotion->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'name', $promotionName),
            sprintf('Promotion with name %s still exist', $promotionName),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true($this->responseChecker->isCreationSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then the :promotion promotion should not applies to discounted items
     */
    public function thePromotionShouldNotAppliesToDiscountedItems(PromotionInterface $promotion): void
    {
        Assert::false(
            $this->responseChecker->getValue($this->client->show(Resources::PROMOTIONS, $promotion->getCode()), 'appliesToDiscounted'),
        );
    }

    /**
     * @When /^I archive the ("([^"]+)" promotion)$/
     */
    public function iArchiveThePromotion(PromotionInterface $promotion): void
    {
        $this->client->customItemAction(Resources::PROMOTIONS, $promotion->getCode(), HttpRequest::METHOD_PATCH, 'archive');
        $this->client->index(Resources::PROMOTIONS);
    }

    /**
     * @When /^I restore the ("([^"]+)" promotion)$/
     */
    public function iRestoreThePromotion(PromotionInterface $promotion): void
    {
        $this->client->customItemAction(Resources::PROMOTIONS, $promotion->getCode(), HttpRequest::METHOD_PATCH, 'restore');
    }

    /**
     * @Then I should be viewing non archival promotions
     */
    public function iShouldBeViewingNonArchivalPromotions(): void
    {
        // Intentionally left blank
    }

    /**
     * @Given I filter archival promotions
     */
    public function iFilterArchivalPromotions(): void
    {
        $this->client->addFilter('exists[archivedAt]', true);
        $this->client->filter();
    }
}
