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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @When I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct(): void
    {
        $this->client->buildCreateRequest('products');
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(string $code = null): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn(string $name, string $language): void
    {
        $data = ['translations' => [$language => ['locale' => $language]]];
        $data['translations'][$language]['name'] = $name;

        $this->client->addCompoundRequestData($data);
    }

    /**
     * @When I set its slug to :slug
     * @When I set its slug to :slug in :language
     * @When I remove its slug
     */
    public function iSetItsSlugTo(?string $slug = null, $language = 'en_US'): void
    {
        $data = ['translations' => [$language => ['locale' => $language, 'slug' => $slug]]];

        $this->client->addCompoundRequestData($data);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @Then I should see the product :productName in the list
     * @Then the product :productName should appear in the store
     * @Then the product :productName should be in the shop
     * @Then this product should still be named :productName
     */
    public function theProductShouldAppearInTheShop(string $productName): void
    {
        $this->client->index('products');

        Assert::true($this->client->hasItemWithTranslation('en_US', 'name', $productName));
    }
}
