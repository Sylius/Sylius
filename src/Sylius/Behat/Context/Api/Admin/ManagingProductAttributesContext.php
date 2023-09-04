<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Product\Model\ProductAttributeInterface;
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
     * @When /^I(?:| try to) delete (this product attribute)$/
     */
    public function iDeleteThisProductAttribute(ProductAttributeInterface $attribute): void
    {
        $this->client->delete(Resources::PRODUCT_ATTRIBUTES, $attribute->getCode());
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

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse());
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(ProductAttributeInterface $productAttribute): void
    {
        $response = $this->client->index(Resources::PRODUCT_ATTRIBUTES);

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'code', $productAttribute->getCode()),
            sprintf('Product attribute with code %s exists, but should not', $productAttribute->getCode()),
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the product attribute is in use.',
        );
    }
}
