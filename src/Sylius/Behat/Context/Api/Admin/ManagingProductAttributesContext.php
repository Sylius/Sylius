<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Webmozart\Assert\Assert;

final class ManagingProductAttributesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When I want to see all product attributes in store
     */
    public function iWantToBrowseProductAttributes(): void
    {
        $this->client->index(Resources::PRODUCT_ATTRIBUTES);
    }

    /**
     * @Then I should see :count product attributes in the list
     */
    public function iShouldSeeCountProductAttributesInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see the product attribute :attributeName in the list
     */
    public function iShouldSeeTheProductAttributeInTheList(string $attributeName): void
    {
        Assert::true($this->responseChecker->hasItemWithTranslation(
            $this->client->getLastResponse(),
            'en_US',
            'name',
            $attributeName,
        ));
    }
}
