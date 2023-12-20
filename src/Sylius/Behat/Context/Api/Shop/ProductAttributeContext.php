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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class ProductAttributeContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithValue(string $attributeName, string $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);
        $attributeValue = $attribute['value'];

        if (is_array($attributeValue)) {
            Assert::inArray($expectedAttribute, $attributeValue);

            return;
        }

        Assert::same($attributeValue, $expectedAttribute);
    }

    /**
     * @Then /^I should(?:| also) see the product attribute "([^"]+)" with (positive|negative) value$/
     */
    public function iShouldSeeTheProductAttributeWithBoolean(string $attributeName, string $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);

        Assert::same($attribute['value'], 'positive' === $expectedAttribute);
    }

    /**
     * @Then /^I should(?:| also) see the product attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function iShouldSeeTheProductAttributeWithPercentage(string $attributeName, int $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);

        Assert::same($attribute['value'], $expectedAttribute / 100);
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute on the list
     */
    public function iShouldSeeTheProductAttributeWithValueOnTheList(string $attributeName, string $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);

        Assert::inArray($expectedAttribute, $attribute['value']);
    }

    /**
     * @Then I should not see the product attribute :attributeName
     */
    public function iShouldNotSeeTheProductAttribute(string $attributeName): void
    {
        Assert::false($this->hasAttributeByName($attributeName));
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with date :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithDate(string $attributeName, string $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);

        Assert::true(new \DateTime($attribute['value']) == new \DateTime($expectedAttribute));
    }

    /**
     * @Then I should see :count attributes
     */
    public function iShouldSeeAttributes(int $count): void
    {
        Assert::count($this->getAttributes(), $count);
    }

    /**
     * @Then the first attribute should be :name
     */
    public function theFirstAttributeShouldBe(string $name): void
    {
        $attributes = $this->getAttributes();
        $attribute = reset($attributes);

        Assert::isArray($attribute);
        Assert::same($attribute['name'], $name);
    }

    /**
     * @Then the last attribute should be :name
     */
    public function theLastAttributeShouldBe(string $name): void
    {
        $attributes = $this->getAttributes();
        $attribute = end($attributes);

        Assert::isArray($attribute);
        Assert::same($attribute['name'], $name);
    }

    private function hasAttributeByName(string $name): bool
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    private function getAttributeByName(string $name): array
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute['name'] === $name) {
                return $attribute;
            }
        }

        throw new InvalidArgumentException('Expected a value other than null.');
    }

    private function getAttributes(): array
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        try {
            $attributes = $this->sharedStorage->get('product_attributes');
        } catch (\InvalidArgumentException) {
            $productAttributesResponse = $this->client->subResourceIndex(Resources::PRODUCTS, 'attributes', $product->getCode());
            $attributes = $this->responseChecker->getCollection($productAttributesResponse);

            $this->sharedStorage->set('product_attributes', $attributes);
        }

        return $attributes;
    }
}
