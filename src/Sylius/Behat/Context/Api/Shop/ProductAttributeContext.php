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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class ProductAttributeContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithValue(string $attributeName, string $expectedAttribute): void
    {
        $attribute = $this->getAttributeByName($attributeName);

        Assert::same($attribute['value'], $expectedAttribute);
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
        Assert::count($this->sharedStorage->get('product_attributes'), $count);
    }

    /**
     * @Then the first attribute should be :name
     */
    public function theFirstAttributeShouldBe(string $name): void
    {
        $attributes = $this->sharedStorage->get('product_attributes');
        $attribute = reset($attributes);

        Assert::isArray($attribute);
        Assert::same($attribute['name'], $name);
    }

    /**
     * @Then the last attribute should be :name
     */
    public function theLastAttributeShouldBe(string $name): void
    {
        $attributes = $this->sharedStorage->get('product_attributes');
        $attribute = end($attributes);

        Assert::isArray($attribute);
        Assert::same($attribute['name'], $name);
    }

    private function getAttributeByName(string $name): array
    {
        foreach ($this->sharedStorage->get('product_attributes') as $attribute) {
            if ($attribute['name'] === $name) {
                return $attribute;
            }
        }

        throw new InvalidArgumentException('Expected a value other than null.');
    }
}
